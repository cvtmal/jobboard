<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Enums\Workplace;
use App\Models\Applicant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Applicant>
 */
final class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'mobile_phone' => $this->faker->boolean(70) ? $this->faker->phoneNumber() : null,
            'headline' => $this->faker->boolean(80) ? $this->faker->jobTitle() : null,
            'bio' => $this->faker->boolean(60) ? $this->faker->paragraphs(2, true) : null,
            'work_permit' => $this->faker->boolean(90),
            'employment_type_preference' => $this->faker->boolean(80) ? $this->faker->randomElement(EmploymentType::cases()) : null,
            'workplace_preference' => $this->faker->boolean(80) ? $this->faker->randomElement(Workplace::cases()) : null,
            'available_from' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            'salary_expectation' => $this->faker->boolean(60) ? $this->faker->numberBetween(30000, 150000) : null,
            'resume_path' => $this->faker->boolean(50) ? 'uploads/resumes/'.$this->faker->uuid().'.pdf' : null,
            'profile_photo_path' => $this->faker->boolean(40) ? 'uploads/profile_photos/'.$this->faker->uuid().'.jpg' : null,
            'portfolio_url' => $this->faker->boolean(30) ? $this->faker->url() : null,
            'linkedin_url' => $this->faker->boolean(40) ? 'https://linkedin.com/in/'.$this->faker->userName() : null,
            'github_url' => $this->faker->boolean(30) ? 'https://github.com/'.$this->faker->userName() : null,
            'website_url' => $this->faker->boolean(20) ? $this->faker->url() : null,
            'date_of_birth' => $this->faker->boolean(70) ? $this->faker->dateTimeBetween('-60 years', '-18 years') : null,
            'address' => $this->faker->boolean(50) ? $this->faker->streetAddress() : null,
            'city' => $this->faker->boolean(60) ? $this->faker->city() : null,
            'state' => $this->faker->boolean(60) ? $this->faker->randomElement(['Zurich', 'Geneva', 'Basel', 'Bern', 'Lausanne']) : null,
            'postal_code' => $this->faker->boolean(60) ? $this->faker->postcode() : null,
            'country' => $this->faker->boolean(70) ? $this->faker->country() : null,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): self
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set the applicant's workplace preference to remote.
     */
    public function preferRemote(): self
    {
        return $this->state(fn (array $attributes): array => [
            'workplace_preference' => Workplace::REMOTE,
        ]);
    }

    /**
     * Set the applicant's workplace preference to onsite.
     */
    public function preferOnsite(): self
    {
        return $this->state(fn (array $attributes): array => [
            'workplace_preference' => Workplace::ONSITE,
        ]);
    }

    /**
     * Set the applicant's workplace preference to hybrid.
     */
    public function preferHybrid(): self
    {
        return $this->state(fn (array $attributes): array => [
            'workplace_preference' => Workplace::HYBRID,
        ]);
    }

    /**
     * Set the applicant's employment type preference.
     */
    public function preferEmploymentType(EmploymentType $type): self
    {
        return $this->state(fn (array $attributes): array => [
            'employment_type_preference' => $type,
        ]);
    }

    /**
     * Set the applicant as available immediately.
     */
    public function availableImmediately(): self
    {
        return $this->state(fn (array $attributes): array => [
            'available_from' => now(),
        ]);
    }

    /**
     * Set the applicant as having a work permit.
     */
    public function withWorkPermit(): self
    {
        return $this->state(fn (array $attributes): array => [
            'work_permit' => true,
        ]);
    }

    /**
     * Set the applicant as not having a work permit.
     */
    public function withoutWorkPermit(): self
    {
        return $this->state(fn (array $attributes): array => [
            'work_permit' => false,
        ]);
    }
}
