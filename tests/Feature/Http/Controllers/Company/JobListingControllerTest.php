<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Company;

use App\Enums\JobStatus;
use App\Enums\Workplace;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class JobListingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_user_can_view_job_listings_index(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $jobListing = JobListing::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)
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
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('company.job-listings.create'));

        $response->assertStatus(200)
            ->assertInertia(fn ($assert) => $assert
                ->component('company/job-listings/create')
            );
    }

    public function test_company_user_can_create_job_listing(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->post(route('company.job-listings.store'), [
                'title' => 'Test Job Listing',
                'description' => 'This is a test job description',
                'workplace' => Workplace::REMOTE->value,
                'status' => JobStatus::DRAFT->value,
                'application_process' => 'internal',
                'category' => 'software_engineering',
                'office_location' => 'Zurich',
                'application_language' => 'english',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('job_listings', [
            'company_id' => $company->id,
            'title' => 'Test Job Listing',
        ]);
    }

    public function test_company_user_can_view_their_job_listing(): void
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'title' => 'Test Job Listing',
        ]);

        $response = $this->actingAs($user)
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
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($user)
            ->put(route('company.job-listings.update', $jobListing), [
                'title' => 'Updated Title',
                'description' => $jobListing->description,
                'workplace' => $jobListing->workplace,
                'status' => $jobListing->status,
                'application_process' => $jobListing->application_process,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('job_listings', [
            'id' => $jobListing->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_company_user_cannot_manage_other_companies_job_listing(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $company1 = Company::factory()->create(['user_id' => $user1->id]);
        $company2 = Company::factory()->create(['user_id' => $user2->id]);
        $jobListing = JobListing::factory()->create(['company_id' => $company2->id]);

        // Try to view
        $response = $this->actingAs($user1)
            ->get(route('company.job-listings.show', $jobListing));
        $response->assertForbidden();

        // Try to edit
        $response = $this->actingAs($user1)
            ->get(route('company.job-listings.edit', $jobListing));
        $response->assertForbidden();

        // Try to update
        $response = $this->actingAs($user1)
            ->put(route('company.job-listings.update', $jobListing), [
                'title' => 'Unauthorized Update',
            ]);
        $response->assertForbidden();

        // Try to delete
        $response = $this->actingAs($user1)
            ->delete(route('company.job-listings.destroy', $jobListing));
        $response->assertForbidden();
    }
}
