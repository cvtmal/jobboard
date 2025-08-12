<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\Company;
use App\Models\JobListing;
use App\Models\JobListingSubscription;
use App\Models\JobTier;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;

final class CreateJobListingWithSubscriptionAction
{
    /**
     * Create a new job listing with a subscription.
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
            $selectedTierId = $data['selected_tier_id'] ?? null;
            unset($data['selected_tier_id']);

            $jobData = [
                'company_id' => $company->id,
                'title' => $data['title'],
                'workload_min' => $data['workload_min'],
                'workload_max' => $data['workload_max'],
                'description' => $this->formatDescription($data),
                'workplace' => $data['workplace'],
                'city' => $data['office_location'] ?? null,
            ];

            if (isset($data['employment_type'])) {
                $jobData['employment_type'] = $data['employment_type'];
            }

            if (isset($data['seniority_level'])) {
                $jobData['experience_level'] = match ($data['seniority_level']) {
                    'no_experience' => 'entry',
                    'junior' => 'junior',
                    'mid_level' => 'mid-level',
                    'professional' => 'mid-level',
                    'senior' => 'senior',
                    'lead' => 'executive',
                    default => 'mid-level',
                };
            }

            if (! empty($data['salary_min'])) {
                $jobData['salary_min'] = $data['salary_min'];
            }

            if (! empty($data['salary_max'])) {
                $jobData['salary_max'] = $data['salary_max'];
            }

            if (isset($data['salary_period'])) {
                $jobData['salary_type'] = $data['salary_period'];
            }

            if (! empty($data['contact_person'])) {
                $jobData['contact_person'] = $data['contact_person'];
            }

            if (isset($data['application_documents'])) {
                $jobData['application_documents'] = $data['application_documents'];
            }

            if (isset($data['screening_questions'])) {
                $jobData['screening_questions'] = $data['screening_questions'];
            }

            if (isset($data['application_process'])) {
                $jobData['application_process'] = $data['application_process'];
            }

            if (! empty($data['application_email'])) {
                $jobData['application_email'] = $data['application_email'];
            }

            if (! empty($data['application_url'])) {
                $jobData['application_url'] = $data['application_url'];
            }

            if (isset($data['status'])) {
                $jobData['status'] = $data['status'];
            }

            $jobListing = JobListing::create($jobData);

            if ($selectedTierId) {
                /** @var JobTier $tier */
                $tier = JobTier::findOrFail($selectedTierId);

                JobListingSubscription::create([
                    'job_listing_id' => $jobListing->id,
                    'job_tier_id' => $tier->id,
                    'purchased_at' => now(),
                    'expires_at' => now()->addDays($tier->duration_days),
                    'price_paid' => $tier->price,
                    'discount_applied' => 0.00,
                    'payment_status' => JobListingSubscription::STATUS_COMPLETED,
                    'payment_method' => 'credit_card',
                    'transaction_id' => 'sim_'.mb_strtoupper(uniqid()), // Simulate transaction ID
                ]);
            }

            Log::log('info', 'Job listing created with subscription', [
                'job_listing_id' => $jobListing->id,
                'company_id' => $company->id,
                'tier_id' => $selectedTierId,
            ]);

            return $jobListing;
        });
    }

    /**
     * Format the description combining all provided text fields.
     *
     * @param  array<string, mixed>  $data
     */
    private function formatDescription(array $data): string
    {
        $sections = [];

        // Add the merged job description and requirements (required)
        if (! empty($data['description_and_requirements'])) {
            $sections[] = $data['description_and_requirements'];
        }

        // Add benefits if provided
        if (! empty($data['benefits'])) {
            $sections[] = "\n\n## Benefits\n\n".$data['benefits'];
        }

        // Combine all sections
        return implode('', $sections);
    }
}
