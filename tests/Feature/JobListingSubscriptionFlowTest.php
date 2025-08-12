<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\JobListingSubscription;
use App\Models\JobTier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->tier = JobTier::factory()->create([
        'name' => 'Premium',
        'price' => 99.99,
        'duration_days' => 30,
    ]);
});

it('can create a job listing with subscription via API', function () {
    // Authenticate as the company user
    $this->actingAs($this->company, 'company');

    $jobData = [
        'company_id' => $this->company->id,
        'title' => 'Test Job with Subscription',
        'workload_min' => 40,
        'workload_max' => 40,
        'description_and_requirements' => 'This is a test job description with requirements.',
        'benefits' => 'Great benefits package',
        'workplace' => 'hybrid',
        'office_location' => 'Zurich',
        'employment_type' => 'permanent',
        'application_process' => 'email',
        'application_email' => 'jobs@company.com',
        'status' => 'draft',
        'selected_tier_id' => $this->tier->id,
    ];

    $response = $this->post(route('company.job-listings.store-with-subscription'), $jobData);

    expect($response->status())->toBe(302); // Redirect after creation

    $jobListing = $this->company->jobListings()->latest()->first();
    expect($jobListing)->not->toBeNull();
    expect($jobListing->title)->toBe('Test Job with Subscription');

    $subscription = JobListingSubscription::where('job_listing_id', $jobListing->id)->first();
    expect($subscription)->not->toBeNull();
    expect($subscription->job_tier_id)->toBe($this->tier->id);
    expect($subscription->price_paid)->toBe('99.99');
    expect($subscription->payment_status)->toBe(JobListingSubscription::STATUS_COMPLETED);
});

it('can update a job listing with subscription change', function () {
    // Authenticate as the company user
    $this->actingAs($this->company, 'company');

    // Create initial job listing with a subscription
    $initialTier = JobTier::factory()->create([
        'name' => 'Basic',
        'price' => 49.99,
        'duration_days' => 15,
    ]);

    $jobListing = $this->company->jobListings()->create([
        'title' => 'Original Job',
        'workload_min' => 40,
        'workload_max' => 40,
        'description' => 'Original description',
        'workplace' => 'remote',
        'city' => 'Remote',
        'employment_type' => 'permanent',
        'application_process' => 'email',
        'application_email' => 'jobs@company.com',
        'status' => 'draft',
    ]);

    JobListingSubscription::create([
        'job_listing_id' => $jobListing->id,
        'job_tier_id' => $initialTier->id,
        'purchased_at' => now(),
        'expires_at' => now()->addDays($initialTier->duration_days),
        'price_paid' => $initialTier->price,
        'payment_status' => JobListingSubscription::STATUS_COMPLETED,
        'payment_method' => 'credit_card',
        'transaction_id' => 'test_123',
    ]);

    // Update job listing with new subscription tier
    $updateData = [
        'title' => 'Updated Job',
        'workload_min' => 40,
        'workload_max' => 40,
        'description_and_requirements' => 'Updated description and requirements',
        'workplace' => 'hybrid',
        'office_location' => 'Zurich',
        'employment_type' => 'permanent',
        'application_process' => 'email',
        'application_email' => 'jobs@company.com',
        'status' => 'draft',
        'selected_tier_id' => $this->tier->id, // Change to premium tier
    ];

    $response = $this->post(route('company.job-listings.update-with-subscription', $jobListing), $updateData);

    expect($response->status())->toBe(302); // Redirect after update

    $jobListing->refresh();
    expect($jobListing->title)->toBe('Updated Job');

    // Check that the old subscription was expired and new one created
    $activeSubscription = $jobListing->activeSubscription();
    expect($activeSubscription)->not->toBeNull();
    expect($activeSubscription->job_tier_id)->toBe($this->tier->id);
    expect($activeSubscription->price_paid)->toBe('99.99');
});

it('handles job listing creation without subscription', function () {
    // Authenticate as the company user
    $this->actingAs($this->company, 'company');

    $jobData = [
        'company_id' => $this->company->id,
        'title' => 'Test Job without Subscription',
        'workload_min' => 40,
        'workload_max' => 40,
        'description_and_requirements' => 'This is a test job description with requirements.',
        'workplace' => 'remote',
        'office_location' => 'Remote',
        'employment_type' => 'permanent',
        'application_process' => 'email',
        'application_email' => 'jobs@company.com',
        'status' => 'draft',
        // No selected_tier_id provided
    ];

    $response = $this->post(route('company.job-listings.store-with-subscription'), $jobData);

    expect($response->status())->toBe(302); // Redirect after creation

    $jobListing = $this->company->jobListings()->latest()->first();
    expect($jobListing)->not->toBeNull();
    expect($jobListing->title)->toBe('Test Job without Subscription');

    $subscriptionCount = JobListingSubscription::where('job_listing_id', $jobListing->id)->count();
    expect($subscriptionCount)->toBe(0);
});
