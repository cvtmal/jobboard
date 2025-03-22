<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\Applicant;
use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
final class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_listing_id' => JobListing::factory(),
            'applicant_id' => Applicant::factory(),
            'cv_path' => 'uploads/resumes/'.$this->faker->uuid().'.pdf',
            'cover_letter_path' => $this->faker->boolean(70) ? 'uploads/cover_letters/'.$this->faker->uuid().'.pdf' : null,
            'additional_documents_path' => $this->faker->boolean(30) ? 'uploads/additional_documents/'.$this->faker->uuid().'.zip' : null,
            'status' => $this->faker->randomElement(ApplicationStatus::cases()),
            'applied_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    /**
     * Set the application status to a specific value.
     */
    public function withStatus(ApplicationStatus $status): self
    {
        return $this->state([
            'status' => $status,
        ]);
    }

    /**
     * Create an application in the NEW status.
     */
    public function asNew(): self
    {
        return $this->withStatus(ApplicationStatus::NEW);
    }

    /**
     * Create an application in the PENDING status.
     */
    public function pending(): self
    {
        return $this->withStatus(ApplicationStatus::PENDING);
    }

    /**
     * Create an application in the SHORTLISTED status.
     */
    public function shortlisted(): self
    {
        return $this->withStatus(ApplicationStatus::SHORTLISTED);
    }

    /**
     * Create an application in the HIRED status.
     */
    public function hired(): self
    {
        return $this->withStatus(ApplicationStatus::HIRED);
    }

    /**
     * Create an application in the REJECTED status.
     */
    public function rejected(): self
    {
        return $this->withStatus(ApplicationStatus::REJECTED);
    }
}
