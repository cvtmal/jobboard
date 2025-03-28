<?php

declare(strict_types=1);

use App\Actions\JobListing\DeleteJobListingAction;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('deletes a job listing', function () {
    $company = Company::factory()->create();
    $jobListing = JobListing::factory()->create([
        'company_id' => $company->id,
    ]);

    $jobListingId = $jobListing->id;

    $action = new DeleteJobListingAction();
    $action->execute($company, $jobListing);

    $this->assertDatabaseMissing('job_listings', [
        'id' => $jobListingId,
    ]);

    expect(JobListing::find($jobListingId))->toBeNull();
});
