<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Actions\JobListing\CreateCustomJobListingAction;
use App\Actions\JobListing\DeleteJobListingAction;
use App\Actions\JobListing\UpdateJobListingAction;
use App\Enums\JobCategory;
use App\Http\Requests\JobListing\CreateJobListingCustomRequest;
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
use Illuminate\Http\Request;

final class JobListingController
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $company = auth('company')->user();

        $jobListings = JobListing::query()
            ->where('company_id', $company->id)
            ->with('company')
            ->paginate(10);

        return Inertia::render('company/job-listings/index', [
            'jobListings' => $jobListings,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', JobListing::class);

        return Inertia::render('company/job-listings/create', [
            'categoryOptions' => JobCategory::options(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(CreateJobListingCustomRequest $request, CreateCustomJobListingAction $action): RedirectResponse
    {
        $this->authorize('create', JobListing::class);

        $company = $request->user('company');

        /** @var Company $company */
        $jobListing = $action->execute($company, $request->validated());

        return redirect()->route('company.job-listings.screening', $jobListing);
    }

    public function show(JobListing $jobListing): Response
    {
        $this->authorize('view', $jobListing);

        if ($jobListing->company_id !== auth('company')->id()) {
            abort(403);
        }

        $jobListing->load('company');

        return Inertia::render('company/job-listings/show', [
            'jobListing' => $jobListing,
            'categoryLabel' => $jobListing->category?->label(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(JobListing $jobListing): Response
    {
        $this->authorize('update', $jobListing);

        $jobListing->load('company');

        return Inertia::render('company/job-listings/edit', [
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

        return redirect()->route('company.job-listings.show', $jobListing)
            ->with('success', 'Job listing updated successfully.');
    }

    /**
     * @throws AuthorizationException
     */
    public function editScreening(JobListing $jobListing): Response
    {
        $this->authorize('update', $jobListing);

        return Inertia::render('company/job-listings/screening', [
            'jobListing' => $jobListing,
        ]);
    }

    /**
     * @throws Throwable
     * @throws AuthorizationException
     */
    public function updateScreening(Request $request, JobListing $jobListing): RedirectResponse
    {
        $this->authorize('update', $jobListing);

        $jobListing->update($request->validate([
            'application_documents' => 'nullable|array',
            'screening_questions' => 'nullable|array',
        ]));

        return redirect()->route('company.job-listings.show', $jobListing)
            ->with('success', 'Screening questions and application requirements added successfully.');
    }

    /**
     * @throws Throwable
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

        return redirect()->route('company.job-listings.index')
            ->with('success', 'Job listing deleted successfully.');
    }
}
