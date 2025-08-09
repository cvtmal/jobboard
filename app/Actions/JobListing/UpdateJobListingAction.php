<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Enums\JobCategory;
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
            // Convert categories to array format for JSON storage if provided
            if (isset($data['categories'])) {
                $data['categories'] = array_map(
                    fn ($category) => $category instanceof JobCategory ? $category->value : $category,
                    $data['categories']
                );
            }

            $jobListing->update($data);

            return $jobListing;
        });
    }
}
