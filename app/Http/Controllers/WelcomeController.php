<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\JobListing;
use Inertia\Inertia;
use Inertia\Response;

final class WelcomeController
{
    /**
     * Display the welcome page with job listings.
     */
    public function __invoke(): Response
    {
        $jobListings = JobListing::query()
            ->with('company')
            ->where('status', JobStatus::PUBLISHED)
            ->where('active_until', '>', now())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return Inertia::render('welcome', [
            'jobListings' => $jobListings,
        ]);
    }
}
