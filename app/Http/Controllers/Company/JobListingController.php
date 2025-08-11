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
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

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

        $company = auth('company')->user();

        return Inertia::render('company/job-listings/create', [
            'categoryOptions' => JobCategory::options(),
            'companyLogo' => $company?->logo_url,
            'companyBanner' => $company?->banner_url,
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

        return redirect()->route('company.job-listings.show', $jobListing);
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
            'categoryLabels' => $jobListing->jobCategories->map(fn ($category) => $category->label())->toArray(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(JobListing $jobListing): Response
    {
        $this->authorize('update', $jobListing);

        $jobListing->load('company');

        // Transform job listing data to match create form structure
        $formData = $this->transformJobListingToFormData($jobListing);

        return Inertia::render('company/job-listings/edit', [
            'jobListing' => $formData,
            'categoryOptions' => JobCategory::options(),
            'companyLogo' => $jobListing->effective_logo_url,
            'companyBanner' => $jobListing->effective_banner_url,
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

        $updatedJobListing = $action->execute($company, $jobListing, $request->validated());

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

    /**
     * Transform stored job listing data to create form structure
     *
     * @return array<string, mixed>
     */
    private function transformJobListingToFormData(JobListing $jobListing): array
    {
        // Parse description back to separate fields
        $descriptionParts = $this->parseDescriptionFields($jobListing->description);

        // Map experience level back to seniority level
        $seniorityLevel = match ($jobListing->experience_level?->value) {
            'entry' => 'no_experience',
            'junior' => 'junior',
            'mid-level' => 'mid_level',
            'senior' => 'senior',
            'executive' => 'lead',
            default => 'mid_level',
        };

        // Map employment type to create form format
        $employmentType = match ($jobListing->employment_type?->value) {
            'permanent' => 'permanent',
            'temporary' => 'temporary',
            'freelance' => 'freelance',
            'internship' => 'internship',
            'side_job' => 'side-job',
            'apprenticeship' => 'apprenticeship',
            'working_student' => 'working-student',
            'interim' => 'interim',
            default => 'permanent',
        };

        // Map salary type to salary period
        $salaryPeriod = match ($jobListing->salary_type?->value) {
            'hourly' => 'hourly',
            'daily' => 'daily',
            'monthly' => 'monthly',
            'yearly' => 'yearly',
            default => 'yearly',
        };

        return [
            'id' => $jobListing->id,
            'title' => $jobListing->title ?? '',
            'description_and_requirements' => $descriptionParts['description_and_requirements'] ?? '',
            'benefits' => $descriptionParts['benefits'] ?? '',
            'workload_min' => $jobListing->workload_min ?? 80,
            'workload_max' => $jobListing->workload_max ?? 100,

            // Location information
            'workplace' => $jobListing->workplace?->value ?? 'onsite',
            'office_location' => $jobListing->city ?? '',

            // Employment details
            'employment_type' => $employmentType,
            'seniority_level' => $seniorityLevel,

            // Salary information
            'salary_min' => $jobListing->salary_min ? (string) $jobListing->salary_min : '',
            'salary_max' => $jobListing->salary_max ? (string) $jobListing->salary_max : '',
            'salary_period' => $salaryPeriod,

            // Skills
            'skills' => $this->extractSkillsFromDescription() ?? '',

            // Screening and application fields
            'application_documents' => $jobListing->application_documents ?? [
                'cv' => 'required',
                'cover_letter' => 'optional',
            ],
            'screening_questions' => $jobListing->screening_questions ?? [],

            // Application process
            'application_process' => $jobListing->application_process?->value ?? 'email',
            'application_email' => $jobListing->application_email ?? '',
            'application_url' => $jobListing->application_url ?? '',
            'contact_person' => $jobListing->contact_person ?? '',

            // Status
            'status' => $jobListing->status?->value ?? 'published',

            // Company ID for validation
            'company_id' => $jobListing->company_id,
        ];
    }

    /**
     * Parse the stored description back into separate fields
     *
     * @return array<string, string>
     */
    private function parseDescriptionFields(string $description): array
    {
        $parts = [
            'description_and_requirements' => '',
            'benefits' => '',
        ];

        // Split by the benefits section marker
        $sections = explode("\n\n## Benefits\n\n", $description, 2);

        $parts['description_and_requirements'] = mb_trim($sections[0]);

        if (isset($sections[1])) {
            $parts['benefits'] = mb_trim($sections[1]);
        }

        return $parts;
    }

    /**
     * Extract skills from description (placeholder implementation)
     * In a real scenario, you might store skills separately
     */
    private function extractSkillsFromDescription(): string
    {
        // This is a placeholder since skills aren't stored separately in the current structure
        // You might want to implement a more sophisticated extraction or store skills separately
        return '';
    }
}
