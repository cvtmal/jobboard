<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class JobListingPolicy
{
    use HandlesAuthorization;

    public function view(Company $company, JobListing $jobListing): Response
    {
        return Response::allow();
    }

    public function edit(Company $company, JobListing $jobListing): Response
    {
        return $company->id === $jobListing->company_id
            ? Response::allow()
            : Response::deny('You do not own this job.');
    }

    public function update(Company $company, JobListing $jobListing): Response
    {
        return $company->id === $jobListing->company_id
            ? Response::allow()
            : Response::deny('You do not own this job.');
    }

    public function delete(Company $company, JobListing $jobListing): Response
    {
        return $company->id === $jobListing->company_id
            ? Response::allow()
            : Response::deny('You do not own this job.');
    }

    public function create(Company $company): Response
    {
        return Response::allow();
    }
}
