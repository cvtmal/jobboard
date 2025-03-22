<?php

declare(strict_types=1);

use App\Enums\JobStatus;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

test('job listing model exists', function (): void {
    $jobListing = new JobListing();

    expect($jobListing)->toBeInstanceOf(JobListing::class);
});

test('job listing factory creates valid instance', function (): void {
    $jobListing = JobListing::factory()->create();

    expect($jobListing)
        ->toBeInstanceOf(JobListing::class)
        ->title->not->toBeEmpty()
        ->description->not->toBeEmpty()
        ->reference_number->toContain('JOB-');

    // Additional checks for specific fields
    // Some fields may be null since they're optional
    if ($jobListing->employment_type !== null) {
        expect($jobListing->employment_type->value)->toBeIn(
            array_column(App\Enums\EmploymentType::cases(), 'value')
        );
    }

    if ($jobListing->workplace !== null) {
        expect($jobListing->workplace->value)->toBeIn(
            array_column(App\Enums\Workplace::cases(), 'value')
        );
    }

    if ($jobListing->experience_level !== null) {
        expect($jobListing->experience_level->value)->toBeIn(
            array_column(App\Enums\ExperienceLevel::cases(), 'value')
        );
    }

    expect($jobListing->status->value)->toBeIn(
        array_column(JobStatus::cases(), 'value')
    );
});

test('job listing belongs to a company', function (): void {
    $company = Company::factory()->create();
    $jobListing = JobListing::factory()->create(['company_id' => $company->id]);

    expect($jobListing->company)
        ->toBeInstanceOf(Company::class)
        ->id->toBe($company->id);

    expect($jobListing->company())->toBeInstanceOf(BelongsTo::class);
});

test('job listing factory published state works', function (): void {
    $jobListing = JobListing::factory()->published()->create();

    expect($jobListing->status)->toBe(JobStatus::PUBLISHED);
});

test('job listing factory draft state works', function (): void {
    $jobListing = JobListing::factory()->draft()->create();

    expect($jobListing->status)->toBe(JobStatus::DRAFT);
});

test('job listing salary_currency has default value in database', function (): void {
    // Create a new job listing without specifying salary_currency
    // We need to do a direct DB insert to test the default value
    $jobId = DB::table('job_listings')->insertGetId([
        'company_id' => Company::factory()->create()->id,
        'title' => 'Test Job',
        'description' => 'Test Description',
        'created_at' => now(),
        'updated_at' => now(),
        'status' => JobStatus::DRAFT->value,
    ]);

    $jobListing = JobListing::find($jobId);

    expect($jobListing->salary_currency)->toBe('CHF');
});

test('job listing salary_currency can be set to other currencies', function (): void {
    $jobListing = JobListing::factory()->create([
        'salary_currency' => 'EUR',
    ]);

    expect($jobListing->salary_currency)->toBe('EUR');
});
