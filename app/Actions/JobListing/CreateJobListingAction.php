<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateJobListingAction
{
    /**
     * Create a new job listing.
     *
     * @param  Company  $company  The company creating the job listing
     * @param  array<string, mixed>  $data  The validated job listing data
     * @return JobListing The newly created job listing
     *
     * @throws Throwable
     */
    public function execute(Company $company, array $data): JobListing
    {
        return DB::transaction(function () use ($company, $data): JobListing {
            $data['company_id'] = $company->id;

            return JobListing::create($data);
        });
    }
}
