<?php

declare(strict_types=1);

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\JobTier;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

test('job listing belongs to a job tier', function (): void {
    $jobTier = JobTier::factory()->create();
    $jobListing = JobListing::factory()->create(['job_tier_id' => $jobTier->id]);

    expect($jobListing->jobTier)
        ->toBeInstanceOf(JobTier::class)
        ->id->toBe($jobTier->id);

    expect($jobListing->jobTier())->toBeInstanceOf(BelongsTo::class);
});

test('job listing has many applications', function (): void {
    $jobListing = JobListing::factory()->create();
    
    // Create a few applications for this job listing
    JobApplication::factory()->count(3)->create([
        'job_listing_id' => $jobListing->id,
    ]);

    expect($jobListing->applications)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(JobApplication::class);

    expect($jobListing->applications())->toBeInstanceOf(HasMany::class);
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

test('job listing casts date fields properly', function (): void {
    $now = now();
    $jobListing = JobListing::factory()->create([
        'active_from' => $now,
        'active_until' => $now->addWeek(),
    ]);

    expect($jobListing->active_from)->toBeInstanceOf(\Carbon\CarbonInterface::class)
        ->and($jobListing->active_until)->toBeInstanceOf(\Carbon\CarbonInterface::class);
});

test('job listing casts numeric fields properly', function (): void {
    $jobListing = JobListing::factory()->create([
        'workload_min' => '20',
        'workload_max' => '40',
        'experience_years_min' => '2',
        'experience_years_max' => '5',
        'salary_min' => '80000.50',
        'salary_max' => '100000.75',
    ]);

    expect($jobListing->workload_min)->toBeInt()->toBe(20)
        ->and($jobListing->workload_max)->toBeInt()->toBe(40)
        ->and($jobListing->experience_years_min)->toBeInt()->toBe(2)
        ->and($jobListing->experience_years_max)->toBeInt()->toBe(5)
        ->and($jobListing->salary_min)->toBeString()->toBe('80000.50')
        ->and($jobListing->salary_max)->toBeString()->toBe('100000.75');
});

test('job listing casts boolean fields properly', function (): void {
    $jobListing = JobListing::factory()->create([
        'no_salary' => true,
    ]);

    expect($jobListing->no_salary)->toBeBool()->toBeTrue();

    $jobListing = JobListing::factory()->create([
        'no_salary' => false,
    ]);

    expect($jobListing->no_salary)->toBeBool()->toBeFalse();
});

test('job listing casts languages as array', function (): void {
    $languages = [
        ['language' => 'English', 'level' => 'Fluent'],
        ['language' => 'German', 'level' => 'Intermediate'],
    ];

    $jobListing = JobListing::factory()->create([
        'languages' => $languages,
    ]);

    expect($jobListing->languages)->toBeArray()
        ->and($jobListing->languages)->toBe($languages);
});

test('job listing casts enum fields properly', function (): void {
    $jobListing = JobListing::factory()->create([
        'employment_type' => EmploymentType::FULL_TIME,
        'workplace' => Workplace::HYBRID,
        'experience_level' => ExperienceLevel::MID_LEVEL,
        'salary_type' => SalaryType::YEARLY,
        'salary_option' => SalaryOption::RANGE,
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::PUBLISHED,
    ]);

    expect($jobListing->employment_type)->toBeInstanceOf(EmploymentType::class)
        ->and($jobListing->employment_type)->toBe(EmploymentType::FULL_TIME)
        
        ->and($jobListing->workplace)->toBeInstanceOf(Workplace::class)
        ->and($jobListing->workplace)->toBe(Workplace::HYBRID)
        
        ->and($jobListing->experience_level)->toBeInstanceOf(ExperienceLevel::class)
        ->and($jobListing->experience_level)->toBe(ExperienceLevel::MID_LEVEL)
        
        ->and($jobListing->salary_type)->toBeInstanceOf(SalaryType::class)
        ->and($jobListing->salary_type)->toBe(SalaryType::YEARLY)
        
        ->and($jobListing->salary_option)->toBeInstanceOf(SalaryOption::class)
        ->and($jobListing->salary_option)->toBe(SalaryOption::RANGE)
        
        ->and($jobListing->application_process)->toBeInstanceOf(ApplicationProcess::class)
        ->and($jobListing->application_process)->toBe(ApplicationProcess::EMAIL)
        
        ->and($jobListing->status)->toBeInstanceOf(JobStatus::class)
        ->and($jobListing->status)->toBe(JobStatus::PUBLISHED);
});

test('job listing has no fillable restrictions (uses guarded empty array)', function (): void {
    $reflectionClass = new ReflectionClass(JobListing::class);
    $guardedProperty = $reflectionClass->getProperty('guarded');
    $guardedProperty->setAccessible(true);
    $guarded = $guardedProperty->getValue(new JobListing());

    expect($guarded)->toBeArray()->toBeEmpty();
});
