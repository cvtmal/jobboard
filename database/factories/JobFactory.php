<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobTier;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
final class JobFactory extends Factory
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
            'languages' => fake()->optional()->randomElements(['English', 'German', 'French', 'Spanish', 'Italian'], random_int(1, 3)),
            'address' => fake()->optional()->streetAddress(),
            'postcode' => fake()->optional()->postcode(),
            'city' => fake()->optional()->city(),
            'no_salary' => fake()->boolean(30), // 30% chance of not showing salary
            'salary_type' => fake()->optional()->randomElement(SalaryType::cases()),
            'salary_option' => fake()->optional()->randomElement(SalaryOption::cases()),
            'salary_min' => fake()->optional()->randomFloat(2, 30000, 80000),
            'salary_max' => fake()->optional()->randomFloat(2, 80000, 150000),
            'salary_currency' => fake()->optional(0.9, 'CHF')->randomElement(['CHF', 'EUR', 'USD', 'GBP']),
            'job_tier' => fake()->optional()->randomElement(JobTier::cases()),
            'application_process' => fake()->randomElement(ApplicationProcess::cases()),
            'application_email' => fake()->optional()->safeEmail(),
            'application_url' => fake()->optional()->url(),
            'contact_person' => fake()->optional()->name(),
            'contact_email' => fake()->optional()->safeEmail(),
            'internal_notes' => fake()->optional()->paragraph(),
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
}
