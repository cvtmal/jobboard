<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\JobListing;
use Inertia\Inertia;
use Inertia\Response;

final class PublicJobListingController
{
    /**
     * Display the public view of a job listing.
     */
    public function show(JobListing $jobListing): Response
    {
        // Only show published jobs to the public
        if ($jobListing->status !== JobStatus::PUBLISHED) {
            abort(404);
        }

        $jobListing->load('company');

        return Inertia::render('Jobs/Show', [
            'jobListing' => $jobListing,
        ]);
    }
}
