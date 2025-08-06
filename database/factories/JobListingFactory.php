<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobCategory;
use App\Enums\JobStatus;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\SwissCanton;
use App\Enums\SwissSubRegion;
use App\Enums\Workplace;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\JobTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobListing>
 */
final class JobListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->jobTitle();
        $companyId = Company::factory();

        return [
            'company_id' => $companyId,
            'category' => fake()->optional(0.9)->randomElement(JobCategory::cases()),
            'reference_number' => 'JOB-'.Str::upper(Str::random(8)),
            'title' => $title,
            'description' => fake()->paragraphs(3, true),
            'employment_type' => fake()->optional()->randomElement(EmploymentType::cases()),
            'workload_min' => fake()->optional()->numberBetween(20, 80),
            'workload_max' => fake()->optional()->numberBetween(80, 100),
            'active_from' => now(),
            'active_until' => fake()->optional()->dateTimeBetween('+1 month', '+6 months'),
            'workplace' => fake()->optional()->randomElement(Workplace::cases()),
            'hierarchy' => fake()->optional()->randomElement(['Reports to CTO', 'Reports to CEO', 'Team Lead', 'Department Manager']),
            'experience_level' => fake()->optional()->randomElement(ExperienceLevel::cases()),
            'experience_years_min' => fake()->optional()->numberBetween(0, 5),
            'experience_years_max' => fake()->optional()->numberBetween(5, 15),
            'education_level' => fake()->optional()->randomElement(['High School', 'Bachelor\'s degree', 'Master\'s degree', 'PhD']),
            'languages' => fake()->optional()->passthrough([
                ['language' => 'English', 'level' => 'Fluent'],
                ['language' => 'German', 'level' => 'Native'],
            ]),
            'address' => fake()->optional()->streetAddress(),
            'postcode' => fake()->optional()->postcode(),
            'city' => fake()->optional()->city(),
            'primary_canton_code' => fake()->optional(0.9)->randomElement(SwissCanton::cases()),
            'primary_sub_region' => function (array $attributes) {
                if (isset($attributes['primary_canton_code'])) {
                    $canton = $attributes['primary_canton_code'];
                    $subRegions = SwissSubRegion::forCanton($canton);
                    if ($subRegions !== []) {
                        return fake()->randomElement($subRegions);
                    }
                }

                return null;
            },
            'primary_latitude' => fake()->optional()->latitude(45.8, 47.8),
            'primary_longitude' => fake()->optional()->longitude(5.9, 10.5),
            'has_multiple_locations' => fake()->boolean(20), // 20% chance of having multiple locations
            'allows_remote' => fake()->boolean(30), // 30% chance of allowing remote work
            'no_salary' => fake()->boolean(30), // 30% chance of not showing salary
            'salary_type' => fake()->optional()->randomElement(SalaryType::cases()),
            'salary_option' => fake()->optional()->randomElement(SalaryOption::cases()),
            'salary_min' => fake()->optional()->randomFloat(2, 30000, 80000),
            'salary_max' => fake()->optional()->randomFloat(2, 80000, 150000),
            'salary_currency' => fake()->optional(0.9, 'CHF')->randomElement(['CHF', 'EUR', 'USD', 'GBP']),
            'job_tier_id' => fake()->optional()->randomElement(JobTier::all()->pluck('id')->toArray()),
            'application_process' => fake()->randomElement(ApplicationProcess::cases()),
            'application_email' => fake()->optional()->safeEmail(),
            'application_url' => fake()->optional()->url(),
            'contact_person' => fake()->optional()->name(),
            'contact_email' => fake()->optional()->safeEmail(),
            'internal_notes' => fake()->optional()->paragraph(),
            // Image settings - default to using company images
            'use_company_logo' => true,
            'use_company_banner' => true,
            'status' => fake()->randomElement(JobStatus::cases()),
        ];
    }

    /**
     * Indicate that the job is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => JobStatus::PUBLISHED,
        ]);
    }

    /**
     * Indicate that the job is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => JobStatus::DRAFT,
        ]);
    }

    /**
     * Indicate that the job is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => JobStatus::EXPIRED,
        ]);
    }

    /**
     * Indicate that the job is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => JobStatus::CLOSED,
        ]);
    }
}
