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
use Illuminate\Support\Facades\Storage;

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
            array_column(EmploymentType::cases(), 'value')
        );
    }

    if ($jobListing->workplace !== null) {
        expect($jobListing->workplace->value)->toBeIn(
            array_column(Workplace::cases(), 'value')
        );
    }

    if ($jobListing->experience_level !== null) {
        expect($jobListing->experience_level->value)->toBeIn(
            array_column(ExperienceLevel::cases(), 'value')
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

    expect($jobListing->active_from)->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($jobListing->active_until)->toBeInstanceOf(Carbon\CarbonInterface::class);
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
        'employment_type' => EmploymentType::PERMANENT,
        'workplace' => Workplace::HYBRID,
        'experience_level' => ExperienceLevel::MID_LEVEL,
        'salary_type' => SalaryType::YEARLY,
        'salary_option' => SalaryOption::RANGE,
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::PUBLISHED,
    ]);

    expect($jobListing->employment_type)->toBeInstanceOf(EmploymentType::class)
        ->and($jobListing->employment_type)->toBe(EmploymentType::PERMANENT)

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

// Image functionality tests
test('job listing defaults to using company images', function (): void {
    $jobListing = JobListing::factory()->create();

    expect($jobListing->use_company_logo)->toBeTrue()
        ->and($jobListing->use_company_banner)->toBeTrue();
});

test('job listing can use custom images', function (): void {
    $jobListing = JobListing::factory()->create([
        'use_company_logo' => false,
        'use_company_banner' => false,
        'logo_path' => 'job-listing-images/logos/test-logo.jpg',
        'banner_path' => 'job-listing-images/banners/test-banner.jpg',
    ]);

    expect($jobListing->use_company_logo)->toBeFalse()
        ->and($jobListing->use_company_banner)->toBeFalse()
        ->and($jobListing->logo_path)->toBe('job-listing-images/logos/test-logo.jpg')
        ->and($jobListing->banner_path)->toBe('job-listing-images/banners/test-banner.jpg');
});

test('job listing hasCustomLogo method works correctly', function (): void {
    Storage::fake('public');

    // Job listing without custom logo
    $jobListing = JobListing::factory()->create();
    expect($jobListing->hasCustomLogo())->toBeFalse();

    // Job listing with custom logo path but file doesn't exist
    $jobListing = JobListing::factory()->create([
        'logo_path' => 'job-listing-images/logos/nonexistent.jpg',
    ]);
    expect($jobListing->hasCustomLogo())->toBeFalse();

    // Job listing with custom logo and file exists
    $logoPath = 'job-listing-images/logos/test-logo.jpg';
    Storage::disk('public')->put($logoPath, 'fake logo content');

    $jobListing = JobListing::factory()->create([
        'logo_path' => $logoPath,
    ]);
    expect($jobListing->hasCustomLogo())->toBeTrue();
});

test('job listing hasCustomBanner method works correctly', function (): void {
    Storage::fake('public');

    // Job listing without custom banner
    $jobListing = JobListing::factory()->create();
    expect($jobListing->hasCustomBanner())->toBeFalse();

    // Job listing with custom banner path but file doesn't exist
    $jobListing = JobListing::factory()->create([
        'banner_path' => 'job-listing-images/banners/nonexistent.jpg',
    ]);
    expect($jobListing->hasCustomBanner())->toBeFalse();

    // Job listing with custom banner and file exists
    $bannerPath = 'job-listing-images/banners/test-banner.jpg';
    Storage::disk('public')->put($bannerPath, 'fake banner content');

    $jobListing = JobListing::factory()->create([
        'banner_path' => $bannerPath,
    ]);
    expect($jobListing->hasCustomBanner())->toBeTrue();
});

test('job listing effective logo URL falls back to company logo', function (): void {
    Storage::fake('public');

    // Create company with logo
    $companyLogoPath = 'company-images/logos/company-logo.jpg';
    Storage::disk('public')->put($companyLogoPath, 'fake company logo');

    $company = Company::factory()->create([
        'logo_path' => $companyLogoPath,
    ]);

    // Job listing using company logo
    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
        'use_company_logo' => true,
    ]);

    expect($jobListing->effective_logo_url)->toBe($company->logo_url);
});

test('job listing effective logo URL uses custom logo when available', function (): void {
    Storage::fake('public');

    // Create company with logo
    $companyLogoPath = 'company-images/logos/company-logo.jpg';
    Storage::disk('public')->put($companyLogoPath, 'fake company logo');

    $company = Company::factory()->create([
        'logo_path' => $companyLogoPath,
    ]);

    // Create custom logo for job listing
    $jobLogoPath = 'job-listing-images/logos/job-logo.jpg';
    Storage::disk('public')->put($jobLogoPath, 'fake job logo');

    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
        'use_company_logo' => false,
        'logo_path' => $jobLogoPath,
    ]);

    expect($jobListing->effective_logo_url)->toBe(Storage::disk('public')->url($jobLogoPath))
        ->and($jobListing->effective_logo_url)->not->toBe($company->logo_url);
});

test('job listing effective banner URL falls back to company banner', function (): void {
    Storage::fake('public');

    // Create company with banner
    $companyBannerPath = 'company-images/banners/company-banner.jpg';
    Storage::disk('public')->put($companyBannerPath, 'fake company banner');

    $company = Company::factory()->create([
        'banner_path' => $companyBannerPath,
    ]);

    // Job listing using company banner
    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
        'use_company_banner' => true,
    ]);

    expect($jobListing->effective_banner_url)->toBe($company->banner_url);
});

test('job listing effective banner URL uses custom banner when available', function (): void {
    Storage::fake('public');

    // Create company with banner
    $companyBannerPath = 'company-images/banners/company-banner.jpg';
    Storage::disk('public')->put($companyBannerPath, 'fake company banner');

    $company = Company::factory()->create([
        'banner_path' => $companyBannerPath,
    ]);

    // Create custom banner for job listing
    $jobBannerPath = 'job-listing-images/banners/job-banner.jpg';
    Storage::disk('public')->put($jobBannerPath, 'fake job banner');

    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
        'use_company_banner' => false,
        'banner_path' => $jobBannerPath,
    ]);

    expect($jobListing->effective_banner_url)->toBe(Storage::disk('public')->url($jobBannerPath))
        ->and($jobListing->effective_banner_url)->not->toBe($company->banner_url);
});

test('job listing deleteCustomLogo method works correctly', function (): void {
    Storage::fake('public');

    // Create custom logo for job listing
    $jobLogoPath = 'job-listing-images/logos/job-logo.jpg';
    Storage::disk('public')->put($jobLogoPath, 'fake job logo');

    $jobListing = JobListing::factory()->create([
        'use_company_logo' => false,
        'logo_path' => $jobLogoPath,
        'logo_original_name' => 'original-logo.jpg',
        'logo_file_size' => 12345,
        'logo_mime_type' => 'image/jpeg',
        'logo_dimensions' => ['width' => 400, 'height' => 400],
        'logo_uploaded_at' => now(),
    ]);

    expect($jobListing->hasCustomLogo())->toBeTrue();

    $result = $jobListing->deleteCustomLogo();

    expect($result)->toBeTrue();

    $jobListing->refresh();

    expect($jobListing->logo_path)->toBeNull()
        ->and($jobListing->logo_original_name)->toBeNull()
        ->and($jobListing->logo_file_size)->toBeNull()
        ->and($jobListing->logo_mime_type)->toBeNull()
        ->and($jobListing->logo_dimensions)->toBeNull()
        ->and($jobListing->logo_uploaded_at)->toBeNull()
        ->and($jobListing->use_company_logo)->toBeTrue()
        ->and($jobListing->hasCustomLogo())->toBeFalse();

    Storage::disk('public')->assertMissing($jobLogoPath);
});

test('job listing deleteCustomBanner method works correctly', function (): void {
    Storage::fake('public');

    // Create custom banner for job listing
    $jobBannerPath = 'job-listing-images/banners/job-banner.jpg';
    Storage::disk('public')->put($jobBannerPath, 'fake job banner');

    $jobListing = JobListing::factory()->create([
        'use_company_banner' => false,
        'banner_path' => $jobBannerPath,
        'banner_original_name' => 'original-banner.jpg',
        'banner_file_size' => 54321,
        'banner_mime_type' => 'image/jpeg',
        'banner_dimensions' => ['width' => 1200, 'height' => 400],
        'banner_uploaded_at' => now(),
    ]);

    expect($jobListing->hasCustomBanner())->toBeTrue();

    $result = $jobListing->deleteCustomBanner();

    expect($result)->toBeTrue();

    $jobListing->refresh();

    expect($jobListing->banner_path)->toBeNull()
        ->and($jobListing->banner_original_name)->toBeNull()
        ->and($jobListing->banner_file_size)->toBeNull()
        ->and($jobListing->banner_mime_type)->toBeNull()
        ->and($jobListing->banner_dimensions)->toBeNull()
        ->and($jobListing->banner_uploaded_at)->toBeNull()
        ->and($jobListing->use_company_banner)->toBeTrue()
        ->and($jobListing->hasCustomBanner())->toBeFalse();

    Storage::disk('public')->assertMissing($jobBannerPath);
});

test('job listing image casts work correctly', function (): void {
    $now = now();
    $dimensions = ['width' => 400, 'height' => 400];

    $jobListing = JobListing::factory()->create([
        'use_company_logo' => false,
        'use_company_banner' => false,
        'logo_dimensions' => $dimensions,
        'logo_uploaded_at' => $now,
        'banner_dimensions' => $dimensions,
        'banner_uploaded_at' => $now,
    ]);

    expect($jobListing->use_company_logo)->toBeFalse()
        ->and($jobListing->use_company_banner)->toBeFalse()
        ->and($jobListing->logo_dimensions)->toBeArray()->toBe($dimensions)
        ->and($jobListing->logo_uploaded_at)->toBeInstanceOf(Carbon\CarbonInterface::class)
        ->and($jobListing->banner_dimensions)->toBeArray()->toBe($dimensions)
        ->and($jobListing->banner_uploaded_at)->toBeInstanceOf(Carbon\CarbonInterface::class);
});
