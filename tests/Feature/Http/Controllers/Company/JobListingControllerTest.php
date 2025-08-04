<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Enums\JobStatus;
use App\Enums\Workplace;
use App\Models\Company;
use App\Models\JobListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class JobListingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_user_can_view_job_listings_index(): void
    {
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($company, 'company')
            ->get(route('company.job-listings.index'));

        $response->assertStatus(200)
            ->assertInertia(fn ($assert) => $assert
                ->component('company/job-listings/index')
                ->has('jobListings.data', 1)
                ->has('jobListings.data.0', fn ($assert) => $assert
                    ->where('id', $jobListing->id)
                    ->where('title', $jobListing->title)
                    ->etc()
                )
            );
    }

    public function test_company_user_can_view_job_listing_create_form(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->get(route('company.job-listings.create'));

        $response->assertStatus(200)
            ->assertInertia(fn ($assert) => $assert
                ->component('company/job-listings/create')
            );
    }

    public function test_company_user_can_create_job_listing(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($company, 'company')
            ->post(route('company.job-listings.store'), [
                'title' => 'Test Job Listing',
                'description' => 'This is a test job description',
                'workload_min' => 80,
                'workload_max' => 100,
                'requirements' => 'Experience with PHP and Laravel',
                'workplace' => Workplace::REMOTE->value,
                'office_location' => 'Zurich',
                'application_language' => 'english',
                'category' => 'software_engineering',
                'employment_type' => 'permanent',
                'status' => JobStatus::DRAFT->value,
                'application_process' => 'email',
                'company_id' => $company->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('job_listings', [
            'company_id' => $company->id,
            'title' => 'Test Job Listing',
        ]);
    }

    public function test_company_user_can_view_their_job_listing(): void
    {
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'title' => 'Test Job Listing',
        ]);

        $response = $this->actingAs($company, 'company')
            ->get(route('company.job-listings.show', $jobListing));

        $response->assertStatus(200)
            ->assertInertia(fn ($assert) => $assert
                ->component('company/job-listings/show')
                ->has('jobListing', fn ($assert) => $assert
                    ->where('id', $jobListing->id)
                    ->where('title', $jobListing->title)
                    ->etc()
                )
            );
    }

    public function test_company_user_can_update_their_job_listing(): void
    {
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($company, 'company')
            ->put(route('company.job-listings.update', $jobListing), [
                'title' => 'Updated Title',
                'description' => $jobListing->description,
                'workplace' => $jobListing->workplace?->value,
                'status' => $jobListing->status->value,
                'application_process' => $jobListing->application_process->value,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('job_listings', [
            'id' => $jobListing->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_company_user_cannot_manage_other_companies_job_listing(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $jobListing = JobListing::factory()->create(['company_id' => $company2->id]);

        // Try to view
        $response = $this->actingAs($company1, 'company')
            ->get(route('company.job-listings.show', $jobListing));
        $response->assertForbidden();

        // Try to edit
        $response = $this->actingAs($company1, 'company')
            ->get(route('company.job-listings.edit', $jobListing));
        $response->assertForbidden();

        // Try to update
        $response = $this->actingAs($company1, 'company')
            ->put(route('company.job-listings.update', $jobListing), [
                'title' => 'Unauthorized Update',
                'description' => 'Some description',
                'application_process' => 'email',
                'status' => 'published',
            ]);
        $response->assertForbidden();

        // Try to delete
        $response = $this->actingAs($company1, 'company')
            ->delete(route('company.job-listings.destroy', $jobListing));
        $response->assertForbidden();
    }
}
