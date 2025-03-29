<?php

declare(strict_types=1);

use App\Models\JobListing;
use App\Models\JobTier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('job tier has correct casts', function () {
    $jobTier = new JobTier();

    expect($jobTier->getCasts())
        ->toHaveKey('price', 'float')
        ->toHaveKey('featured', 'boolean')
        ->toHaveKey('has_analytics', 'boolean')
        ->toHaveKey('max_applications', 'integer')
        ->toHaveKey('max_active_jobs', 'integer')
        ->toHaveKey('duration_days', 'integer');
});

test('job tier has many job listings', function () {
    $jobTier = new JobTier();

    expect($jobTier->jobs())
        ->toBeInstanceOf(HasMany::class)
        ->and(get_class($jobTier->jobs()->getRelated()))
        ->toBe(JobListing::class);
});

test('job tier factory creates valid instances', function () {
    $jobTier = JobTier::factory()->create();

    expect($jobTier)
        ->toBeInstanceOf(JobTier::class)
        ->name->not->toBeEmpty()
        ->price->toBeFloat()
        ->duration_days->toBeInt()
        ->featured->toBeBool()
        ->max_active_jobs->toBeInt()
        ->has_analytics->toBeBool();

    // Description and max_applications can be null
    if ($jobTier->description !== null) {
        expect($jobTier->description)->toBeString();
    }

    if ($jobTier->max_applications !== null) {
        expect($jobTier->max_applications)->toBeInt();
    }
});

test('job tier can have associated job listings', function () {
    $jobTier = JobTier::factory()->create();
    $jobListings = JobListing::factory()->count(3)->create([
        'job_tier_id' => $jobTier->id,
    ]);

    expect($jobTier->jobs)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(3)
        ->and($jobTier->jobs->pluck('id')->toArray())
        ->toEqual($jobListings->pluck('id')->toArray());
});

test('job tier returns empty collection when no job listings exist', function () {
    $jobTier = JobTier::factory()->create();

    expect($jobTier->jobs)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0)
        ->toBeEmpty();
});
