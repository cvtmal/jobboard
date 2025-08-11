<?php

declare(strict_types=1);

use App\Actions\JobListing\CreateCustomJobListingAction;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new CreateCustomJobListingAction();
    $this->company = Company::factory()->create();
});

it('creates a custom job listing with all fields', function () {
    $data = [
        'title' => 'Senior Laravel Developer',
        'workload_min' => 80,
        'workload_max' => 100,
        'description_and_requirements' => 'We are looking for a senior Laravel developer with 5+ years experience.',
        'benefits' => 'Health insurance, flexible hours, remote work options.',
        'workplace' => 'hybrid',
        'office_location' => 'Zurich',
        'employment_type' => 'permanent',
        'seniority_level' => 'senior',
        'salary_min' => 90000,
        'salary_max' => 120000,
        'salary_type' => 'annual',
        'contact_person' => 'John Smith',
        'application_documents' => ['cv', 'cover_letter'],
        'screening_questions' => ['Why Laravel?', 'Remote work experience?'],
        'application_process' => 'email',
        'application_email' => 'jobs@company.com',
        'application_url' => 'https://company.com/apply',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing)->toBeInstanceOf(JobListing::class);
    expect($jobListing->company_id)->toBe($this->company->id);
    expect($jobListing->title)->toBe('Senior Laravel Developer');
    expect($jobListing->workload_min)->toBe(80);
    expect($jobListing->workload_max)->toBe(100);
    expect($jobListing->description)->toBe('We are looking for a senior Laravel developer with 5+ years experience.'."\n\n## Benefits\n\n".'Health insurance, flexible hours, remote work options.');
    expect($jobListing->workplace)->toBe('hybrid');
    expect($jobListing->city)->toBe('Zurich');
    expect($jobListing->employment_type)->toBe('full-time');
    expect($jobListing->experience_level)->toBe('senior');
    expect($jobListing->salary_min)->toBe(90000);
    expect($jobListing->salary_max)->toBe(120000);
    expect($jobListing->salary_type)->toBe('annual');
    expect($jobListing->contact_person)->toBe('John Smith');
    expect($jobListing->application_documents)->toBe(['cv', 'cover_letter']);
    expect($jobListing->screening_questions)->toBe(['Why Laravel?', 'Remote work experience?']);
    expect($jobListing->application_process)->toBe('email');
    expect($jobListing->application_email)->toBe('jobs@company.com');
    expect($jobListing->application_url)->toBe('https://company.com/apply');
});

it('maps seniority levels correctly', function () {
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
            'title' => 'Test Position',
            'workload_min' => 100,
            'workload_max' => 100,
            'description_and_requirements' => 'Test description',
            'workplace' => 'onsite',
            'office_location' => 'Bern',
            'seniority_level' => $seniorityLevel,
        ];

        $jobListing = $this->action->execute($this->company, $data);

        expect($jobListing->experience_level)->toBe($expectedExperienceLevel);

        // Clean up for next iteration
        $jobListing->delete();
    }
});

it('formats description correctly with benefits', function () {
    $data = [
        'title' => 'Developer Position',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Main job description and requirements.',
        'benefits' => 'Great benefits package.',
        'workplace' => 'remote',
        'office_location' => 'Geneva',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    $expectedDescription = 'Main job description and requirements.'."\n\n## Benefits\n\n".'Great benefits package.';
    expect($jobListing->description)->toBe($expectedDescription);
});

it('formats description correctly without benefits', function () {
    $data = [
        'title' => 'Developer Position',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Only the main description.',
        'workplace' => 'onsite',
        'office_location' => 'Basel',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->description)->toBe('Only the main description.');
});

it('handles empty description_and_requirements', function () {
    $data = [
        'title' => 'Test Position',
        'workload_min' => 80,
        'workload_max' => 100,
        'workplace' => 'hybrid',
        'office_location' => 'Lausanne',
        'benefits' => 'Only benefits provided.',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->description)->toBe("\n\n## Benefits\n\n".'Only benefits provided.');
});

it('handles optional salary fields correctly', function () {
    $data = [
        'title' => 'Position Without Salary',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'No salary specified',
        'workplace' => 'onsite',
        'office_location' => 'St. Gallen',
        'salary_min' => '', // Empty string
        'salary_max' => '', // Empty string
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->salary_min)->toBeNull();
    expect($jobListing->salary_max)->toBeNull();
});

it('handles empty contact person correctly', function () {
    $data = [
        'title' => 'Position Without Contact',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'No contact person specified',
        'workplace' => 'remote',
        'office_location' => 'Zug',
        'contact_person' => '', // Empty string
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->contact_person)->toBeNull();
});

it('handles empty application URLs and emails correctly', function () {
    $data = [
        'title' => 'Position Without Application Links',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'No application links',
        'workplace' => 'onsite',
        'office_location' => 'Lucerne',
        'application_email' => '', // Empty string
        'application_url' => '', // Empty string
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->application_email)->toBeNull();
    expect($jobListing->application_url)->toBeNull();
});

it('preserves non-empty application URLs and emails', function () {
    $data = [
        'title' => 'Position With Application Links',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'Has application links',
        'workplace' => 'hybrid',
        'office_location' => 'Winterthur',
        'application_email' => 'apply@company.com',
        'application_url' => 'https://company.com/careers',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->application_email)->toBe('apply@company.com');
    expect($jobListing->application_url)->toBe('https://company.com/careers');
});

it('runs in database transaction', function () {
    // Mock JobListing to throw an exception
    $this->mock(JobListing::class, function ($mock) {
        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Database error'));
    });

    $data = [
        'title' => 'Failed Job',
        'workload_min' => 100,
        'workload_max' => 100,
        'description_and_requirements' => 'This should fail',
        'workplace' => 'onsite',
        'office_location' => 'Bern',
    ];

    expect(fn () => $this->action->execute($this->company, $data))
        ->toThrow(Exception::class, 'Database error');

    // Verify no job listing was created due to transaction rollback
    $this->assertDatabaseMissing('job_listings', [
        'title' => 'Failed Job',
    ]);
});

it('creates job listing with minimal required fields', function () {
    $data = [
        'title' => 'Minimal Job',
        'workload_min' => 50,
        'workload_max' => 100,
        'description_and_requirements' => 'Minimal description',
        'workplace' => 'remote',
        'office_location' => 'Anywhere',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->title)->toBe('Minimal Job');
    expect($jobListing->workload_min)->toBe(50);
    expect($jobListing->workload_max)->toBe(100);
    expect($jobListing->description)->toBe('Minimal description');
    expect($jobListing->workplace)->toBe('remote');
    expect($jobListing->city)->toBe('Anywhere');
    expect($jobListing->employment_type)->toBeNull();
    expect($jobListing->experience_level)->toBeNull();
    expect($jobListing->salary_min)->toBeNull();
    expect($jobListing->salary_max)->toBeNull();
});
