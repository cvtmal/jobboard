<?php

declare(strict_types=1);

use App\Actions\JobListing\CreateJobListingAction;
use App\Enums\ApplicationProcess;
use App\Enums\JobStatus;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('it creates a job listing', function (): void {
    // Arrange
    $company = Company::factory()->create();

    $data = [
        'title' => 'PHP Developer',
        'description' => 'We are looking for a PHP developer',
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::DRAFT,
    ];

    // Act
    $action = new CreateJobListingAction();
    $jobListing = $action->execute($company, $data);

    // Assert
    expect($jobListing)
        ->toBeInstanceOf(JobListing::class)
        ->id->toBeInt()
        ->title->toBe('PHP Developer')
        ->company_id->toBe($company->id)
        ->application_process->value->toBe(ApplicationProcess::EMAIL->value)
        ->status->value->toBe(JobStatus::DRAFT->value)
        ->and(DB::table('job_listings')->where('id', $jobListing->id)->exists())->toBeTrue();
});
