<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateCustomJobListingAction
{
    /**
     * Create a new job listing with custom fields.
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
            // Map the custom fields to database fields
            $jobData = [
                'company_id' => $company->id,
                'title' => $data['title'],
                'workload_min' => $data['workload_min'],
                'workload_max' => $data['workload_max'],
                'description' => $this->formatDescription($data),
                'workplace' => $data['workplace'],
                'status' => $data['status'],
                'application_process' => $data['application_process'],
                'city' => $data['office_location'],
            ];

            // Add optional fields
            if (isset($data['employment_type'])) {
                $jobData['employment_type'] = $data['employment_type'];
            }

            if (isset($data['seniority_level'])) {
                // Map seniority level to experience level
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

            // Add salary information if provided
            if (! empty($data['salary_min'])) {
                $jobData['salary_min'] = $data['salary_min'];
            }

            if (! empty($data['salary_max'])) {
                $jobData['salary_max'] = $data['salary_max'];
            }

            if (isset($data['salary_type'])) {
                $jobData['salary_type'] = $data['salary_type'];
            }

            // Add categories if provided
            if (! empty($data['categories'])) {
                $jobData['categories'] = $data['categories'];
            }

            // Add screening data if provided (Step 4)
            if (isset($data['application_documents'])) {
                $jobData['application_documents'] = $data['application_documents'];
            }

            if (isset($data['screening_questions'])) {
                $jobData['screening_questions'] = $data['screening_questions'];
            }

            // Create the job listing
            $jobListing = JobListing::create($jobData);

            // Process skills if provided
            if (! empty($data['skills'])) {
                // Future implementation: Associate skills with the job listing
            }

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

        // Add company description if provided
        if (! empty($data['company_description'])) {
            $sections[] = "## About Us\n\n".$data['company_description'];
        }

        // Add job description (required)
        $sections[] = "## Job Description\n\n".$data['description'];

        // Add requirements (required)
        $sections[] = "## Requirements\n\n".$data['requirements'];

        // Add benefits if provided
        if (! empty($data['benefits'])) {
            $sections[] = "## Benefits\n\n".$data['benefits'];
        }

        // Add final words if provided
        if (! empty($data['final_words'])) {
            $sections[] = "## Additional Information\n\n".$data['final_words'];
        }

        // Combine all sections with double line breaks
        return implode("\n\n", $sections);
    }
}
