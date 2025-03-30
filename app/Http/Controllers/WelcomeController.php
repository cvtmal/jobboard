<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class WelcomeController
{
    /**
     * Display the welcome page with job listings.
     */
    public function __invoke(Request $request): Response
    {
        $jobListings = JobListing::query()
            ->with('company')
            ->where('status', JobStatus::PUBLISHED)
            ->where('active_until', '>', now())
            ->orderByDesc('created_at')
            ->limit(10) // Limit to 10 most recent job listings for the welcome page
            ->get();

        return Inertia::render('welcome', [
            'jobListings' => $jobListings,
        ]);
    }
}
