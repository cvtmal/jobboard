<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'company_id' => Company::factory(),
            'reference_number' => 'JOB-'.$this->faker->unique()->numerify('######'),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(3, true),
            'employment_type' => $this->faker->randomElement(['full-time', 'part-time', 'contract', 'freelance', 'internship']),
            'workload_min' => $this->faker->randomElement([80, 90, 100]),
            'workload_max' => 100,
            'active_from' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'active_until' => $this->faker->dateTimeBetween('+1 month', '+3 months'),
            'workplace' => $this->faker->randomElement(['remote', 'on-site', 'hybrid']),
            'hierarchy' => $this->faker->randomElement(['junior', 'mid-level', 'senior', 'lead', 'manager']),
            'experience_level' => $this->faker->randomElement(['entry', 'junior', 'mid', 'senior', 'expert']),
            'experience_years_min' => $this->faker->numberBetween(0, 5),
            'experience_years_max' => $this->faker->numberBetween(5, 10),
            'education_level' => $this->faker->randomElement(['high school', 'bachelor', 'master', 'phd']),
            'languages' => $this->faker->randomElements(['English', 'German', 'French', 'Italian'], $this->faker->numberBetween(1, 3)),
            'address' => $this->faker->streetAddress(),
            'postcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'no_salary' => $this->faker->boolean(20),
            'salary_type' => $this->faker->randomElement(['yearly', 'monthly', 'hourly']),
            'salary_option' => $this->faker->randomElement(['fixed', 'range', 'negotiable']),
            'salary_min' => $this->faker->numberBetween(50000, 70000),
            'salary_max' => $this->faker->numberBetween(70000, 120000),
            'salary_currency' => 'CHF',
            'job_tier' => $this->faker->randomElement(['basic', 'premium', 'enterprise']),
            'application_process' => $this->faker->randomElement(['email', 'website', 'both']),
            'application_email' => $this->faker->companyEmail(),
            'application_url' => $this->faker->url(),
            'contact_person' => $this->faker->name(),
            'contact_email' => $this->faker->email(),
            'internal_notes' => $this->faker->boolean(30) ? $this->faker->paragraph() : null,
            'status' => $this->faker->randomElement(['draft', 'published', 'expired', 'cancelled']),
        ];
    }

    /**
     * Set the job listing to published status.
     */
    public function published(): self
    {
        return $this->state([
            'status' => 'published',
        ]);
    }

    /**
     * Set the job listing to draft status.
     */
    public function draft(): self
    {
        return $this->state([
            'status' => 'draft',
        ]);
    }

    /**
     * Set the job listing to expired status.
     */
    public function expired(): self
    {
        return $this->state([
            'status' => 'expired',
            'active_until' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Set the job listing to cancelled status.
     */
    public function cancelled(): self
    {
        return $this->state([
            'status' => 'cancelled',
        ]);
    }
}
