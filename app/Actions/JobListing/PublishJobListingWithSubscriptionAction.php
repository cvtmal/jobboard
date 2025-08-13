<?php

declare(strict_types=1);

namespace App\Actions\JobListing;

use App\Enums\JobStatus;
use App\Models\JobListing;
use App\Models\JobListingSubscription;
use App\Models\JobTier;
use Illuminate\Support\Facades\DB;
use Throwable;

final class PublishJobListingWithSubscriptionAction
{
    /**
     * Publish a job listing with a subscription.
     *
     * @param  JobListing  $jobListing  The job listing to publish
     * @param  int  $selectedTierId  The selected job tier ID
     * @return JobListing The updated job listing
     *
     * @throws Throwable
     */
    public function execute(JobListing $jobListing, int $selectedTierId): JobListing
    {
        return DB::transaction(function () use ($jobListing, $selectedTierId): JobListing {
            /** @var JobTier $selectedTier */
            $selectedTier = JobTier::findOrFail($selectedTierId);

            $jobListing->update([
                'status' => JobStatus::PUBLISHED,
            ]);

            JobListingSubscription::updateOrCreate(
                ['job_listing_id' => $jobListing->id],
                [
                    'job_tier_id' => $selectedTier->id,
                    'expires_at' => now()->addDays($selectedTier->duration_days),
                    'payment_status' => 'pending', // Simplified for now
                    'purchased_at' => now(),
                    'price_paid' => $selectedTier->price,
                ]
            );

            return $jobListing->fresh();
        });
    }
}
