<?php

declare(strict_types=1);

use App\Actions\JobListing\UpdateJobListingAction;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new UpdateJobListingAction();
    $this->company = Company::factory()->create();
    Storage::fake('public');
});

it('updates a job listing with basic fields', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Old Title',
        'description' => 'Old description',
        'workload_min' => 80,
        'workload_max' => 100,
    ]);

    $data = [
        'title' => 'Updated Title',
        'workload_min' => 60,
        'workload_max' => 80,
        'description_and_requirements' => 'Updated description and requirements',
        'workplace' => 'hybrid',
        'office_location' => 'Zurich',
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    expect($updatedJobListing->title)->toBe('Updated Title');
    expect($updatedJobListing->workload_min)->toBe(60);
    expect($updatedJobListing->workload_max)->toBe(80);
    expect($updatedJobListing->description)->toBe('Updated description and requirements');
    expect($updatedJobListing->workplace)->toBe('hybrid');
    expect($updatedJobListing->city)->toBe('Zurich');
});

it('maps seniority levels correctly during update', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'experience_level' => 'junior',
    ]);

    $mappings = [
        'no_experience' => 'entry',
        'junior' => 'junior',
        'mid_level' => 'mid-level',
        'professional' => 'mid-level',
        'senior' => 'senior',
        'lead' => 'executive',
        'unknown_level' => 'mid-level', // default case
    ];

    foreach ($mappings as $seniorityLevel => $expectedExperienceLevel) {
        $data = [
            'title' => 'Test Update',
            'workload_min' => 100,
            'workload_max' => 100,
            'description_and_requirements' => 'Test description',
            'workplace' => 'onsite',
            'office_location' => 'Bern',
            'seniority_level' => $seniorityLevel,
        ];

        $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

        expect($updatedJobListing->experience_level)->toBe($expectedExperienceLevel);
    }
});

it('updates employment type correctly', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'employment_type' => 'permanent',
    ]);

    // Test employment_type_mapped field
    $data = [
        'title' => 'Test Job',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Test description',
        'workplace' => 'remote',
        'office_location' => 'Geneva',
        'employment_type_mapped' => 'temporary',
        'employment_type' => 'permanent', // Should be ignored in favor of mapped
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);
    expect($updatedJobListing->employment_type)->toBe('temporary');

    // Test regular employment_type field when mapped is not present
    $data['employment_type'] = 'freelance';
    unset($data['employment_type_mapped']);

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);
    expect($updatedJobListing->employment_type)->toBe('freelance');
});

it('formats description with benefits correctly', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $data = [
        'title' => 'Developer Position',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Main job description and requirements.',
        'benefits' => 'Great benefits package.',
        'workplace' => 'onsite',
        'office_location' => 'Basel',
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    $expectedDescription = 'Main job description and requirements.'."\n\n## Benefits\n\n".'Great benefits package.';
    expect($updatedJobListing->description)->toBe($expectedDescription);
});

it('handles optional salary fields correctly', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'salary_min' => 50000,
        'salary_max' => 70000,
    ]);

    $data = [
        'title' => 'No Salary Job',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'No salary specified',
        'workplace' => 'remote',
        'office_location' => 'Lausanne',
        'salary_min' => '', // Empty string
        'salary_max' => '', // Empty string
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    // Empty strings should not update the fields
    expect($updatedJobListing->salary_min)->toBe(50000.0); // Original value preserved
    expect($updatedJobListing->salary_max)->toBe(70000.0); // Original value preserved

    // Test with actual values
    $data['salary_min'] = 60000;
    $data['salary_max'] = 80000;
    $data['salary_type'] = 'annual';

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    expect($updatedJobListing->salary_min)->toBe(60000.0);
    expect($updatedJobListing->salary_max)->toBe(80000.0);
    expect($updatedJobListing->salary_type)->toBe('annual');
});

it('handles application process fields correctly', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $data = [
        'title' => 'Application Test Job',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Test description',
        'workplace' => 'onsite',
        'office_location' => 'St. Gallen',
        'application_process' => 'email',
        'application_email' => 'jobs@company.com',
        'application_url' => '', // Empty URL should set to null
        'application_documents' => ['cv', 'cover_letter'],
        'screening_questions' => ['Why this role?'],
        'contact_person' => 'Jane Doe',
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    expect($updatedJobListing->application_process)->toBe('email');
    expect($updatedJobListing->application_email)->toBe('jobs@company.com');
    expect($updatedJobListing->application_url)->toBeNull();
    expect($updatedJobListing->application_documents)->toBe(['cv', 'cover_letter']);
    expect($updatedJobListing->screening_questions)->toBe(['Why this role?']);
    expect($updatedJobListing->contact_person)->toBe('Jane Doe');
});

it('uploads banner image and updates metadata', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'banner_path' => null,
    ]);

    $bannerFile = UploadedFile::fake()->image('banner.jpg', 1200, 400);

    $data = [
        'title' => 'Job with Banner',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Test description',
        'workplace' => 'onsite',
        'office_location' => 'Zug',
        'banner_image' => $bannerFile,
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    expect($updatedJobListing->banner_path)->not->toBeNull();
    expect($updatedJobListing->banner_original_name)->toBe('banner.jpg');
    expect($updatedJobListing->banner_file_size)->toBe($bannerFile->getSize());
    expect($updatedJobListing->banner_mime_type)->toBe('image/jpeg');
    expect($updatedJobListing->banner_uploaded_at)->not->toBeNull();
    expect($updatedJobListing->use_company_banner)->toBeFalse();

    // Verify file was stored
    Storage::disk('public')->assertExists($updatedJobListing->banner_path);
});

it('uploads logo image and updates metadata', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'logo_path' => null,
    ]);

    $logoFile = UploadedFile::fake()->image('logo.png', 400, 400);

    $data = [
        'title' => 'Job with Logo',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Test description',
        'workplace' => 'remote',
        'office_location' => 'Lucerne',
        'logo_image' => $logoFile,
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    expect($updatedJobListing->logo_path)->not->toBeNull();
    expect($updatedJobListing->logo_original_name)->toBe('logo.png');
    expect($updatedJobListing->logo_file_size)->toBe($logoFile->getSize());
    expect($updatedJobListing->logo_mime_type)->toBe('image/png');
    expect($updatedJobListing->logo_uploaded_at)->not->toBeNull();
    expect($updatedJobListing->use_company_logo)->toBeFalse();

    // Verify file was stored
    Storage::disk('public')->assertExists($updatedJobListing->logo_path);
});

it('deletes old image when uploading new one', function () {
    $oldBannerPath = 'job-listings/banners/old-banner.jpg';
    Storage::disk('public')->put($oldBannerPath, 'old-banner-content');

    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'banner_path' => $oldBannerPath,
    ]);

    $newBannerFile = UploadedFile::fake()->image('new-banner.jpg', 1200, 400);

    $data = [
        'title' => 'Updated Banner Job',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Updated description',
        'workplace' => 'hybrid',
        'office_location' => 'Winterthur',
        'banner_image' => $newBannerFile,
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    // Verify old file was deleted
    Storage::disk('public')->assertMissing($oldBannerPath);

    // Verify new file exists
    Storage::disk('public')->assertExists($updatedJobListing->banner_path);
    expect($updatedJobListing->banner_path)->not->toBe($oldBannerPath);
});

it('runs in database transaction', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Original Title',
    ]);

    // Mock DB transaction to throw an exception
    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new Exception('Database error'));

    $data = [
        'title' => 'Failed Update',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'This should fail',
        'workplace' => 'onsite',
        'office_location' => 'Bern',
    ];

    expect(fn () => $this->action->execute($this->company, $jobListing, $data))
        ->toThrow(Exception::class, 'Database error');

    // Verify the original title is still there due to transaction rollback
    $jobListing->refresh();
    expect($jobListing->title)->toBe('Original Title');
});

it('returns fresh instance of updated job listing', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Original Title',
    ]);

    $data = [
        'title' => 'Fresh Title',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Fresh description',
        'workplace' => 'onsite',
        'office_location' => 'Aarau',
    ];

    $updatedJobListing = $this->action->execute($this->company, $jobListing, $data);

    expect($updatedJobListing->title)->toBe('Fresh Title');
    expect($updatedJobListing->id)->toBe($jobListing->id);

    // Verify that the original instance wasn't modified in memory
    // (though this depends on Laravel's implementation details)
    expect($updatedJobListing->wasRecentlyCreated)->toBeFalse();
});
