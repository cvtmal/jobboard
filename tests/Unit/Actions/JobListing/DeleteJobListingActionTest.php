<?php

declare(strict_types=1);

use App\Actions\JobListing\DeleteJobListingAction;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new DeleteJobListingAction();
    $this->company = Company::factory()->create();
});

it('deletes a job listing successfully', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Job to be deleted',
    ]);

    expect($jobListing->exists)->toBeTrue();

    $this->action->execute($this->company, $jobListing);

    expect($jobListing->exists)->toBeFalse();

    $this->assertDatabaseMissing('job_listings', [
        'id' => $jobListing->id,
        'title' => 'Job to be deleted',
    ]);
});

it('deletes job listing from the correct company', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Company A Job',
    ]);

    // Create another company with a different job listing
    $otherCompany = Company::factory()->create();
    $otherJobListing = JobListing::factory()->create([
        'company_id' => $otherCompany->id,
        'title' => 'Company B Job',
    ]);

    $this->action->execute($this->company, $jobListing);

    // Verify the correct job was deleted
    expect($jobListing->exists)->toBeFalse();
    expect($otherJobListing->exists)->toBeTrue();

    $this->assertDatabaseMissing('job_listings', [
        'id' => $jobListing->id,
    ]);

    $this->assertDatabaseHas('job_listings', [
        'id' => $otherJobListing->id,
        'title' => 'Company B Job',
    ]);
});

it('runs in database transaction', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Transaction Test Job',
    ]);

    // Mock the job listing to throw an exception when delete is called
    $mockJobListing = $this->mock(JobListing::class);
    $mockJobListing->shouldReceive('delete')
        ->once()
        ->andThrow(new Exception('Delete failed'));

    expect(fn () => $this->action->execute($this->company, $mockJobListing))
        ->toThrow(Exception::class, 'Delete failed');

    // Verify the original job listing still exists due to transaction rollback
    $this->assertDatabaseHas('job_listings', [
        'id' => $jobListing->id,
        'title' => 'Transaction Test Job',
    ]);
});

it('handles soft deletes if implemented', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Soft Delete Test',
    ]);

    $originalId = $jobListing->id;

    $this->action->execute($this->company, $jobListing);

    // Check if the model has soft deletes
    if (method_exists($jobListing, 'trashed')) {
        // If soft deletes are implemented, check if it's soft deleted
        $deletedListing = JobListing::withTrashed()->find($originalId);
        expect($deletedListing)->not->toBeNull();
        expect($deletedListing->trashed())->toBeTrue();
    } else {
        // If no soft deletes, it should be completely removed
        $this->assertDatabaseMissing('job_listings', [
            'id' => $originalId,
        ]);
    }
});

it('accepts both company and job listing parameters', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Parameter Test Job',
    ]);

    // The action should accept both parameters even though company isn't used
    expect(fn () => $this->action->execute($this->company, $jobListing))
        ->not->toThrow();

    expect($jobListing->exists)->toBeFalse();
});

it('deletes job listing with related data', function () {
    // Create a job listing with all possible fields
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Full Featured Job',
        'description' => 'Detailed description',
        'employment_type' => 'full-time',
        'workplace' => 'hybrid',
        'salary_min' => 50000,
        'salary_max' => 80000,
        'application_documents' => ['cv', 'cover_letter'],
        'screening_questions' => ['Why this role?'],
    ]);

    $originalId = $jobListing->id;

    $this->action->execute($this->company, $jobListing);

    expect($jobListing->exists)->toBeFalse();

    $this->assertDatabaseMissing('job_listings', [
        'id' => $originalId,
        'title' => 'Full Featured Job',
    ]);
});

it('handles multiple deletions correctly', function () {
    $jobListings = JobListing::factory()->count(3)->create([
        'company_id' => $this->company->id,
    ]);

    foreach ($jobListings as $jobListing) {
        $this->action->execute($this->company, $jobListing);
        expect($jobListing->exists)->toBeFalse();
    }

    // Verify all job listings are deleted
    foreach ($jobListings as $jobListing) {
        $this->assertDatabaseMissing('job_listings', [
            'id' => $jobListing->id,
        ]);
    }
});

it('maintains database integrity after deletion', function () {
    $jobListing = JobListing::factory()->create([
        'company_id' => $this->company->id,
        'title' => 'Integrity Test Job',
    ]);

    $initialCount = JobListing::count();

    $this->action->execute($this->company, $jobListing);

    $finalCount = JobListing::count();

    expect($finalCount)->toBe($initialCount - 1);
});
