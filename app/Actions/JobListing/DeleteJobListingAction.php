<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Support\Facades\DB;

final class DeleteJobListingAction
{
    /**
     * Delete a job listing.
     *
     * @param  Company  $company  The company deleting the job listing
     * @param  JobListing  $jobListing  The job listing to delete
     */
    public function execute(Company $company, JobListing $jobListing): void
    {
        DB::transaction(function () use ($jobListing): void {
            // Delete the job listing
            $jobListing->delete();
        });
    }
}
