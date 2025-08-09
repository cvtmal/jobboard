<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\JobListing\CreateJobListingAction;
use App\Actions\JobListing\DeleteJobListingAction;
use App\Actions\JobListing\UpdateJobListingAction;
use App\Enums\JobCategory;
use App\Http\Requests\JobListing\CreateJobListingRequest;
use App\Http\Requests\JobListing\DeleteJobListingRequest;
use App\Http\Requests\JobListing\UpdateJobListingRequest;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class JobListingController
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $jobListings = JobListing::query()
            ->with('company')
            ->paginate(10);

        return Inertia::render('JobListings/Index', [
            'jobListings' => $jobListings,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('JobListings/Create', [
            'categoryOptions' => JobCategory::options(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(CreateJobListingRequest $request, CreateJobListingAction $action): RedirectResponse
    {
        $company = $request->user('company');

        /** @var Company $company */
        $jobListing = $action->execute($company, $request->validated());

        return redirect()->route('company.job-listings.index', $jobListing)
            ->with('success', 'Job created successfully.');
    }

    public function show(JobListing $jobListing): Response
    {
        $jobListing->load('company', 'jobCategoryPivots');

        return Inertia::render('JobListings/Show', [
            'jobListing' => $jobListing,
            'categoryLabels' => $jobListing->jobCategories->map(fn ($category) => $category->label())->toArray(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(JobListing $jobListing): Response
    {
        $this->authorize('edit', $jobListing);

        $jobListing->load('company');

        return Inertia::render('JobListings/Edit', [
            'jobListing' => $jobListing,
            'categoryOptions' => JobCategory::options(),
        ]);
    }

    /**
     * @throws Throwable
     * @throws AuthorizationException
     */
    public function update(UpdateJobListingRequest $request, JobListing $jobListing, UpdateJobListingAction $action): RedirectResponse
    {
        $this->authorize('update', $jobListing);

        $company = $request->user('company');

        if (! $company instanceof Company) {
            abort(403);
        }

        $action->execute($company, $jobListing, $request->validated());

        return redirect()->route('job-listings.show', $jobListing)
            ->with('success', 'Job listing updated successfully.');
    }

    /**
     * Remove the specified job listing from storage.
     *
     * @throws AuthorizationException
     */
    public function destroy(DeleteJobListingRequest $request, JobListing $jobListing, DeleteJobListingAction $action): RedirectResponse
    {
        $this->authorize('delete', $jobListing);

        $company = $request->user('company');

        if (! $company instanceof Company) {
            abort(403);
        }

        $action->execute($company, $jobListing);

        return redirect()->route('job-listings.index')
            ->with('success', 'Job listing deleted successfully.');
    }
}
