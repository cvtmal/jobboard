<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateJobListingAction
{
    /**
     * Update an existing job listing.
     *
     * @param  Company  $company  The company updating the job listing
     * @param  JobListing  $jobListing  The job listing to update
     * @param  array<string, mixed>  $data  The validated job listing data
     * @return JobListing The updated job listing
     *
     * @throws Throwable
     */
    public function execute(Company $company, JobListing $jobListing, array $data): JobListing
    {
        return DB::transaction(function () use ($jobListing, $data): JobListing {
            $jobListing->update($data);

            return $jobListing;
        });
    }
}
