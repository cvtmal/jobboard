<?php

declare(strict_types=1);

use App\Enums\ApplicationProcess;
use App\Enums\JobStatus;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

describe('index', function () {
    it('displays job listings page with paginated job listings for a company', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListings = JobListing::factory()->count(5)->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->actingAs($company, 'company')
            ->get(route('company.job-listings.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('JobListings/Index')
                ->has('jobListings.data', 5)
                ->has('jobListings.data.0', fn (Assert $listing) => $listing
                    ->has('id')
                    ->has('title')
                    ->has('description')
                    // Only assert fields that we know exist in the data
                    ->etc()
                )
                ->has('jobListings.links')
                ->has('jobListings.current_page')
                ->has('jobListings.last_page')
            );
    });

    it('displays job listings page for authenticated users', function () {
        // Arrange
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $jobListings = JobListing::factory()->count(5)->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->actingAs($user)
            ->get(route('job-listings.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('JobListings/Index')
                ->has('jobListings.data', 5)
                ->has('jobListings.data.0', fn (Assert $listing) => $listing
                    ->has('id')
                    ->has('title')
                    ->has('description')
                    ->etc()
                )
            );
    });

    it('redirects to login page for unauthenticated users', function () {
        // Act & Assert
        $this->get(route('job-listings.index'))
            ->assertRedirect(route('login'));
    });
});

describe('create', function () {
    it('displays job listing creation page for authenticated company', function () {
        // Arrange
        $company = Company::factory()->create();

        // Act & Assert
        $this->actingAs($company, 'company')
            ->get(route('company.job-listings.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('JobListings/Create')
                // Verify no errors are passed initially
                ->where('errors', [])
            );
    });

    it('redirects to login page for unauthenticated users', function () {
        // Act & Assert
        $this->get(route('company.job-listings.create'))
            ->assertRedirect(route('login'));
    });
});

describe('store', function () {
    it('creates a new job listing for authenticated company', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobData = [
            'title' => 'PHP Developer',
            'description' => 'We are looking for a PHP developer to join our team.',
            'application_process' => ApplicationProcess::EMAIL->value,
            'status' => JobStatus::DRAFT->value,
            'company_id' => $company->id,
        ];

        // Act
        $response = $this->actingAs($company, 'company')
            ->post(route('company.job-listings.store'), $jobData);

        // Assert - accept either a redirect with success or a 200 status
        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertStatus(200);
        }

        $this->assertDatabaseHas('job_listings', [
            'title' => 'PHP Developer',
            'company_id' => $company->id,
        ]);
    });

    it('creates a job listing with all form fields including previously problematic enums', function () {
        // Arrange
        $company = Company::factory()->create();

        $jobData = [
            'title' => 'Senior PHP Developer',
            'description' => 'We are looking for an experienced PHP developer to join our team.',
            'address' => 'Bahnhofstrasse 1',
            'city' => 'Zurich',
            'postcode' => '8001',
            'salary_min' => '80000',
            'salary_max' => '120000',
            'salary_type' => 'yearly', // Using the correct enum value
            'employment_type' => 'full-time', // Using the correct enum value
            'experience_level' => 'mid-level', // Using the correct enum value
            'application_process' => ApplicationProcess::EMAIL->value,
            'application_email' => 'careers@example.com',
            'status' => JobStatus::PUBLISHED->value,
            'company_id' => $company->id,
            'no_salary' => false,
        ];

        // Act
        $response = $this->actingAs($company, 'company')
            ->post(route('company.job-listings.store'), $jobData);

        // Assert
        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertStatus(200);
        }

        // Check if the job listing was actually created with all the fields
        $this->assertDatabaseHas('job_listings', [
            'title' => 'Senior PHP Developer',
            'description' => 'We are looking for an experienced PHP developer to join our team.',
            'address' => 'Bahnhofstrasse 1',
            'city' => 'Zurich',
            'postcode' => '8001',
            'salary_min' => 80000,
            'salary_max' => 120000,
            'salary_type' => 'yearly',
            'employment_type' => 'full-time',
            'experience_level' => 'mid-level',
            'application_process' => ApplicationProcess::EMAIL->value,
            'application_email' => 'careers@example.com',
            'status' => JobStatus::PUBLISHED->value,
            'company_id' => $company->id,
            'no_salary' => false,
        ]);
    });

    it('returns validation errors for invalid input', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobData = [
            'title' => '', // Invalid - required
            'description' => '', // Invalid - required
        ];

        // Act & Assert
        $this->actingAs($company, 'company')
            ->post(route('company.job-listings.store'), $jobData)
            ->assertStatus(302) // Redirects with validation errors
            ->assertSessionHasErrors(['title', 'description']);
    });

    it('redirects unauthenticated users', function () {
        // Arrange
        $jobData = [
            'title' => 'PHP Developer',
            'description' => 'We are looking for a PHP developer to join our team.',
        ];

        // Act & Assert
        $this->post(route('company.job-listings.store'), $jobData)
            ->assertRedirect(route('login'));
    });
});

describe('show', function () {
    it('displays a job listing for a company', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->actingAs($company, 'company')
            ->get(route('company.job-listings.show', $jobListing))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('JobListings/Show')
                ->has('jobListing', fn (Assert $listing) => $listing
                    ->has('id')
                    ->has('title')
                    ->has('description')
                    ->has('company_id')
                    ->etc()
                )
            );
    });

    it('displays a job listing for authenticated users', function () {
        // Arrange
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'status' => JobStatus::PUBLISHED->value,
        ]);

        // Act & Assert
        $this->actingAs($user)
            ->get(route('job-listings.show', $jobListing))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('JobListings/Show')
                ->has('jobListing', fn (Assert $listing) => $listing
                    ->has('id')
                    ->has('title')
                    ->has('description')
                    ->etc()
                )
            );
    });

    it('redirects to login page for unauthenticated users', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->get(route('job-listings.show', $jobListing))
            ->assertRedirect(route('login'));
    });
});

describe('edit', function () {
    it('displays job listing edit page for authenticated company', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->actingAs($company, 'company')
            ->get(route('company.job-listings.edit', $jobListing))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('JobListings/Edit')
                ->has('jobListing', fn (Assert $listing) => $listing
                    ->has('id')
                    ->has('title')
                    ->has('description')
                    ->has('company_id')
                    ->etc()
                )
            );
    });

    it('redirects unauthorized users attempting to edit other company listings', function () {
        // Arrange
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company1->id,
        ]);

        // Act & Assert
        $this->actingAs($company2, 'company')
            ->get(route('company.job-listings.edit', $jobListing))
            ->assertForbidden();
    });

    it('redirects unauthenticated users', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->get(route('company.job-listings.edit', $jobListing))
            ->assertRedirect(route('login'));
    });
});

describe('update', function () {
    it('updates a job listing for authenticated company', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'title' => 'Old Title',
            'description' => 'Old Description',
            'application_process' => ApplicationProcess::EMAIL->value,
            'status' => JobStatus::DRAFT->value,
        ]);

        $updateData = [
            'title' => 'Updated PHP Developer',
            'description' => 'Updated job description',
            'application_process' => ApplicationProcess::EMAIL->value,
            'status' => JobStatus::PUBLISHED->value,
        ];

        // Act
        $response = $this->actingAs($company, 'company')
            ->put(route('company.job-listings.update', $jobListing), $updateData);

        // Assert - accept either a redirect with success or a 200 status
        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertStatus(200);
        }

        $this->assertDatabaseHas('job_listings', [
            'id' => $jobListing->id,
            'title' => 'Updated PHP Developer',
            'description' => 'Updated job description',
            'status' => JobStatus::PUBLISHED->value,
        ]);
    });

    it('returns validation errors for invalid input', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
            'application_process' => ApplicationProcess::EMAIL->value,
            'status' => JobStatus::DRAFT->value,
        ]);

        $updateData = [
            'title' => '', // Invalid - required
            'description' => '', // Invalid - required
        ];

        // Act & Assert
        $this->actingAs($company, 'company')
            ->put(route('company.job-listings.update', $jobListing), $updateData)
            ->assertStatus(302) // Redirects with validation errors
            ->assertSessionHasErrors(['title', 'description']);
    });

    it('forbids unauthorized updates', function () {
        // Arrange
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company1->id,
            'application_process' => ApplicationProcess::EMAIL->value,
            'status' => JobStatus::DRAFT->value,
        ]);

        $updateData = [
            'title' => 'Updated PHP Developer',
            'description' => 'Updated job description',
            'application_process' => ApplicationProcess::EMAIL->value,
            'status' => JobStatus::PUBLISHED->value,
        ];

        // Act & Assert
        $this->actingAs($company2, 'company')
            ->put(route('company.job-listings.update', $jobListing), $updateData)
            ->assertForbidden();
    });

    it('redirects unauthenticated users', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        $updateData = [
            'title' => 'Updated PHP Developer',
            'description' => 'Updated job description',
        ];

        // Act & Assert
        $this->put(route('company.job-listings.update', $jobListing), $updateData)
            ->assertRedirect(route('login'));
    });
});

describe('destroy', function () {
    it('deletes a job listing', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $response = $this->actingAs($company, 'company')
            ->delete(route('company.job-listings.destroy', $jobListing));

        // Accept either a redirect or a direct status response
        if ($response->isRedirect()) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertStatus(200);
        }

        $this->assertDatabaseMissing('job_listings', [
            'id' => $jobListing->id,
            'deleted_at' => null,
        ]);
    });

    it('forbids unauthorized deletes', function () {
        // Arrange
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company1->id,
        ]);

        // Act & Assert
        $this->actingAs($company2, 'company')
            ->delete(route('company.job-listings.destroy', $jobListing))
            ->assertForbidden();
    });

    it('redirects unauthenticated users', function () {
        // Arrange
        $company = Company::factory()->create();
        $jobListing = JobListing::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act & Assert
        $this->delete(route('company.job-listings.destroy', $jobListing))
            ->assertRedirect(route('login'));
    });
});
