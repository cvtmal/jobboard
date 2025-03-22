<?php

declare(strict_types=1);

use App\Enums\JobStatus;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

test('job model exists', function (): void {
    $job = new Job();

    expect($job)->toBeInstanceOf(Job::class);
});

test('job factory creates valid instance', function (): void {
    $job = Job::factory()->create();

    expect($job)
        ->toBeInstanceOf(Job::class)
        ->title->not->toBeEmpty()
        ->description->not->toBeEmpty()
        ->reference_number->toContain('JOB-');

    // Additional checks for specific fields
    // Some fields may be null since they're optional
    if ($job->employment_type !== null) {
        expect($job->employment_type->value)->toBeIn(
            array_column(App\Enums\EmploymentType::cases(), 'value')
        );
    }

    if ($job->workplace !== null) {
        expect($job->workplace->value)->toBeIn(
            array_column(App\Enums\Workplace::cases(), 'value')
        );
    }

    if ($job->experience_level !== null) {
        expect($job->experience_level->value)->toBeIn(
            array_column(App\Enums\ExperienceLevel::cases(), 'value')
        );
    }

    expect($job->status->value)->toBeIn(
        array_column(JobStatus::cases(), 'value')
    );
});

test('job belongs to a company', function (): void {
    $company = Company::factory()->create();
    $job = Job::factory()->create(['company_id' => $company->id]);

    expect($job->company)
        ->toBeInstanceOf(Company::class)
        ->id->toBe($company->id);

    expect($job->company())->toBeInstanceOf(BelongsTo::class);
});

test('job factory published state works', function (): void {
    $job = Job::factory()->published()->create();

    expect($job->status)->toBe(JobStatus::PUBLISHED);
});

test('job factory draft state works', function (): void {
    $job = Job::factory()->draft()->create();

    expect($job->status)->toBe(JobStatus::DRAFT);
});
