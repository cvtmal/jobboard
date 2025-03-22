<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\Applicant;
use App\Models\JobApplication;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Use RefreshDatabase trait to handle database transactions
uses(RefreshDatabase::class);

test('job application model exists', function (): void {
    $jobApplication = new JobApplication();

    expect($jobApplication)->toBeInstanceOf(JobApplication::class);
});

test('job application status is casted to enum', function (): void {
    $jobApplication = new JobApplication();
    $jobApplication->status = ApplicationStatus::NEW;

    expect($jobApplication->status)
        ->toBeInstanceOf(ApplicationStatus::class)
        ->toBe(ApplicationStatus::NEW);

    expect($jobApplication->status->value)->toBe('new');
});

test('job application belongs to job listing relationship is defined', function (): void {
    $jobApplication = new JobApplication();

    expect($jobApplication->jobListing())->toBeInstanceOf(BelongsTo::class);
});

test('job application belongs to applicant relationship is defined', function (): void {
    $jobApplication = new JobApplication();

    expect($jobApplication->applicant())->toBeInstanceOf(BelongsTo::class);
});

test('job application can be persisted and retrieved with relationships', function (): void {
    // Create the related models first
    $jobListing = JobListing::factory()->create();
    $applicant = Applicant::factory()->create();

    // Create a job application with the related models
    $jobApplication = new JobApplication();
    $jobApplication->job_listing_id = $jobListing->id;
    $jobApplication->applicant_id = $applicant->id;
    $jobApplication->cv_path = 'uploads/resumes/test.pdf';
    $jobApplication->status = ApplicationStatus::NEW;
    $jobApplication->applied_at = now();
    $jobApplication->save();

    // Refresh from database
    $retrievedApplication = JobApplication::find($jobApplication->id);

    expect($retrievedApplication)
        ->toBeInstanceOf(JobApplication::class)
        ->cv_path->toBe('uploads/resumes/test.pdf')
        ->status->toBe(ApplicationStatus::NEW);

    // Test relationships
    expect($retrievedApplication->jobListing)
        ->toBeInstanceOf(JobListing::class)
        ->id->toBe($jobListing->id);

    expect($retrievedApplication->applicant)
        ->toBeInstanceOf(Applicant::class)
        ->id->toBe($applicant->id);
});

test('job application factory methods exist', function (): void {
    // Test factory methods exist
    expect(method_exists(JobApplication::factory(), 'asNew'))->toBeTrue();
    expect(method_exists(JobApplication::factory(), 'pending'))->toBeTrue();
    expect(method_exists(JobApplication::factory(), 'shortlisted'))->toBeTrue();
    expect(method_exists(JobApplication::factory(), 'hired'))->toBeTrue();
    expect(method_exists(JobApplication::factory(), 'rejected'))->toBeTrue();
});

test('job application factory creates status correctly', function (): void {
    // Create the related models first
    $jobListing = JobListing::factory()->create();
    $applicant = Applicant::factory()->create();

    // Test with factory method state modifiers
    $new = JobApplication::factory()->asNew()->create([
        'job_listing_id' => $jobListing->id,
        'applicant_id' => $applicant->id,
    ]);
    expect($new->status)->toBe(ApplicationStatus::NEW);

    $pending = JobApplication::factory()->pending()->create([
        'job_listing_id' => $jobListing->id,
        'applicant_id' => $applicant->id,
    ]);
    expect($pending->status)->toBe(ApplicationStatus::PENDING);

    $shortlisted = JobApplication::factory()->shortlisted()->create([
        'job_listing_id' => $jobListing->id,
        'applicant_id' => $applicant->id,
    ]);
    expect($shortlisted->status)->toBe(ApplicationStatus::SHORTLISTED);

    $hired = JobApplication::factory()->hired()->create([
        'job_listing_id' => $jobListing->id,
        'applicant_id' => $applicant->id,
    ]);
    expect($hired->status)->toBe(ApplicationStatus::HIRED);

    $rejected = JobApplication::factory()->rejected()->create([
        'job_listing_id' => $jobListing->id,
        'applicant_id' => $applicant->id,
    ]);
    expect($rejected->status)->toBe(ApplicationStatus::REJECTED);
});
