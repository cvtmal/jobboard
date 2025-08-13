<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class UpdateJobListingAction
{
    /**
     * Update an existing job listing with custom fields.
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
            $jobData = [
                'title' => $data['title'],
                'workload_min' => $data['workload_min'],
                'workload_max' => $data['workload_max'],
                'description' => $this->formatDescription($data),
                'workplace' => $data['workplace'],
                'city' => $data['office_location'],
            ];

            if (isset($data['employment_type_mapped'])) {
                $jobData['employment_type'] = $data['employment_type_mapped'];
            } elseif (isset($data['employment_type'])) {
                $jobData['employment_type'] = $data['employment_type'];
            }

            if (isset($data['seniority_level'])) {
                $jobData['experience_level'] = match ($data['seniority_level']) {
                    'no_experience' => 'entry',
                    'junior' => 'junior',
                    'mid_level' => 'mid-level',
                    'professional' => 'professional',
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

            if (isset($data['salary_type'])) {
                $jobData['salary_type'] = $data['salary_type'];
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

            // Add application process fields
            if (isset($data['application_process'])) {
                $jobData['application_process'] = $data['application_process'];
            }

            $jobData['application_email'] = empty($data['application_email']) ? null : $data['application_email'];

            $jobData['application_url'] = empty($data['application_url']) ? null : $data['application_url'];

            if (isset($data['status'])) {
                $jobData['status'] = $data['status'];
            }

            // Handle image uploads
            if (isset($data['banner_image']) && $data['banner_image'] instanceof UploadedFile) {
                // Delete old banner if exists
                if ($jobListing->banner_path && Storage::disk('public')->exists($jobListing->banner_path)) {
                    Storage::disk('public')->delete($jobListing->banner_path);
                }

                $bannerPath = $data['banner_image']->store('job-listings/banners', 'public');
                $jobData['banner_path'] = $bannerPath;
                $jobData['banner_original_name'] = $data['banner_image']->getClientOriginalName();
                $jobData['banner_file_size'] = $data['banner_image']->getSize();
                $jobData['banner_mime_type'] = $data['banner_image']->getMimeType();
                $jobData['banner_uploaded_at'] = now();
                $jobData['use_company_banner'] = false;
            }

            if (isset($data['logo_image']) && $data['logo_image'] instanceof UploadedFile) {
                // Delete old logo if exists
                if ($jobListing->logo_path && Storage::disk('public')->exists($jobListing->logo_path)) {
                    Storage::disk('public')->delete($jobListing->logo_path);
                }

                $logoPath = $data['logo_image']->store('job-listings/logos', 'public');
                $jobData['logo_path'] = $logoPath;
                $jobData['logo_original_name'] = $data['logo_image']->getClientOriginalName();
                $jobData['logo_file_size'] = $data['logo_image']->getSize();
                $jobData['logo_mime_type'] = $data['logo_image']->getMimeType();
                $jobData['logo_uploaded_at'] = now();
                $jobData['use_company_logo'] = false;
            }

            $jobListing->update($jobData);

            return $jobListing->fresh();
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
