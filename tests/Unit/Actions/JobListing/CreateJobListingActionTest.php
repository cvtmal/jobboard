<?php

declare(strict_types=1);

use App\Actions\JobListing\CreateJobListingAction;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new CreateJobListingAction();
    $this->company = Company::factory()->create();
});

it('creates a job listing successfully', function () {
    $data = [
        'title' => 'Senior PHP Developer',
        'description' => 'We are looking for a senior PHP developer to join our team.',
        'employment_type' => 'permanent',
        'experience_level' => 'senior',
        'workplace' => 'hybrid',
        'city' => 'Zurich',
        'salary_min' => 90000,
        'salary_max' => 120000,
        'salary_type' => 'annual',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing)->toBeInstanceOf(JobListing::class);
    expect($jobListing->company_id)->toBe($this->company->id);
    expect($jobListing->title)->toBe('Senior PHP Developer');
    expect($jobListing->description)->toBe('We are looking for a senior PHP developer to join our team.');
    expect($jobListing->employment_type)->toBe('full-time');
    expect($jobListing->experience_level)->toBe('senior');
    expect($jobListing->workplace)->toBe('hybrid');
    expect($jobListing->city)->toBe('Zurich');
    expect($jobListing->salary_min)->toBe(90000);
    expect($jobListing->salary_max)->toBe(120000);
    expect($jobListing->salary_type)->toBe('annual');

    $this->assertDatabaseHas('job_listings', [
        'company_id' => $this->company->id,
        'title' => 'Senior PHP Developer',
        'description' => 'We are looking for a senior PHP developer to join our team.',
    ]);
});

it('creates a job listing with minimal required fields', function () {
    $data = [
        'title' => 'Junior Developer',
        'description' => 'Entry level position for developers.',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing)->toBeInstanceOf(JobListing::class);
    expect($jobListing->company_id)->toBe($this->company->id);
    expect($jobListing->title)->toBe('Junior Developer');
    expect($jobListing->description)->toBe('Entry level position for developers.');
    expect($jobListing->employment_type)->toBeNull();
    expect($jobListing->experience_level)->toBeNull();
    expect($jobListing->salary_min)->toBeNull();
    expect($jobListing->salary_max)->toBeNull();
});

it('automatically sets company_id from provided company', function () {
    $data = [
        'title' => 'Test Position',
        'description' => 'Test description',
        'company_id' => 999, // This should be overridden
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->company_id)->toBe($this->company->id);
    expect($jobListing->company_id)->not->toBe(999);
});

it('preserves all provided data fields', function () {
    $data = [
        'title' => 'Full Stack Developer',
        'description' => 'We need a full stack developer.',
        'employment_type' => 'temporary',
        'experience_level' => 'mid-level',
        'workplace' => 'remote',
        'city' => 'Geneva',
        'country' => 'Switzerland',
        'salary_min' => 50000,
        'salary_max' => 70000,
        'salary_type' => 'annual',
        'application_deadline' => '2024-12-31',
        'contact_person' => 'Jane Doe',
        'application_email' => 'jobs@example.com',
        'application_url' => 'https://jobs.example.com/apply',
        'status' => 'published',
        'application_process' => 'email',
        'application_documents' => ['cv', 'cover_letter'],
        'screening_questions' => ['Why do you want to work here?'],
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->title)->toBe('Full Stack Developer');
    expect($jobListing->description)->toBe('We need a full stack developer.');
    expect($jobListing->employment_type)->toBe('part-time');
    expect($jobListing->experience_level)->toBe('mid-level');
    expect($jobListing->workplace)->toBe('remote');
    expect($jobListing->city)->toBe('Geneva');
    expect($jobListing->country)->toBe('Switzerland');
    expect($jobListing->salary_min)->toBe(50000);
    expect($jobListing->salary_max)->toBe(70000);
    expect($jobListing->salary_type)->toBe('annual');
    expect($jobListing->application_deadline)->toBe('2024-12-31');
    expect($jobListing->contact_person)->toBe('Jane Doe');
    expect($jobListing->application_email)->toBe('jobs@example.com');
    expect($jobListing->application_url)->toBe('https://jobs.example.com/apply');
    expect($jobListing->status)->toBe('published');
    expect($jobListing->application_process)->toBe('email');
    expect($jobListing->application_documents)->toBe(['cv', 'cover_letter']);
    expect($jobListing->screening_questions)->toBe(['Why do you want to work here?']);
});

it('runs in database transaction', function () {
    // Mock DB transaction to throw an exception
    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new Exception('Database error'));

    $data = [
        'title' => 'Test Job',
        'description' => 'Test description',
    ];

    expect(fn () => $this->action->execute($this->company, $data))
        ->toThrow(Exception::class, 'Database error');

    // Verify no job listing was created due to transaction rollback
    $this->assertDatabaseMissing('job_listings', [
        'title' => 'Test Job',
    ]);
});

it('creates job listing with Unicode characters', function () {
    $data = [
        'title' => 'Développeur Zürich',
        'description' => 'Nous cherchons un développeur à Zürich avec expérience.',
        'city' => 'Zürich',
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->title)->toBe('Développeur Zürich');
    expect($jobListing->description)->toBe('Nous cherchons un développeur à Zürich avec expérience.');
    expect($jobListing->city)->toBe('Zürich');
});

it('handles array and object data types correctly', function () {
    $data = [
        'title' => 'Data Engineer',
        'description' => 'We need a data engineer.',
        'application_documents' => ['cv', 'portfolio', 'references'],
        'screening_questions' => [
            'What is your experience with data pipelines?',
            'Have you worked with Spark?',
        ],
    ];

    $jobListing = $this->action->execute($this->company, $data);

    expect($jobListing->application_documents)->toBe(['cv', 'portfolio', 'references']);
    expect($jobListing->screening_questions)->toBe([
        'What is your experience with data pipelines?',
        'Have you worked with Spark?',
    ]);
});
