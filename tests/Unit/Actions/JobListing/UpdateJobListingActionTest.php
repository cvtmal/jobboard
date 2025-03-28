<?php

declare(strict_types=1);

use App\Actions\JobListing\UpdateJobListingAction;
use App\Enums\ApplicationProcess;
use App\Enums\JobStatus;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('it updates a job listing', function (): void {
    // Arrange
    $company = Company::factory()->create();

    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
        'title' => 'PHP Developer',
        'description' => 'Original job description',
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::DRAFT,
    ]);

    $data = [
        'title' => 'Senior PHP Developer',
        'description' => 'Updated job description',
        'application_process' => ApplicationProcess::URL,
        'status' => JobStatus::PUBLISHED,
    ];

    // Act
    $action = new UpdateJobListingAction();
    $updatedJobListing = $action->execute($company, $jobListing, $data);

    // Assert
    expect($updatedJobListing)
        ->toBeInstanceOf(JobListing::class)
        ->id->toBe($jobListing->id)
        ->title->toBe('Senior PHP Developer')
        ->description->toBe('Updated job description')
        ->company_id->toBe($company->id)
        ->application_process->value->toBe(ApplicationProcess::URL->value)
        ->status->value->toBe(JobStatus::PUBLISHED->value)
        ->and(DB::table('job_listings')->where('id', $jobListing->id)->exists())->toBeTrue();
});
