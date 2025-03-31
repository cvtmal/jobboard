<?php

declare(strict_types=1);

use App\Http\Controllers\JobListingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('job-listings', JobListingController::class);
});

// Company-specific job listing routes
Route::middleware(['auth:company', 'verified.company'])->group(function () {
    Route::get('company/job-listings', [JobListingController::class, 'index'])
        ->name('company.job-listings.index');
    Route::get('company/job-listings/create', [JobListingController::class, 'create'])
        ->name('company.job-listings.create');
    Route::post('company/job-listings', [JobListingController::class, 'store'])
        ->name('company.job-listings.store');
    Route::get('company/job-listings/{jobListing}', [JobListingController::class, 'show'])
        ->name('company.job-listings.show');
    Route::get('company/job-listings/{jobListing}/edit', [JobListingController::class, 'edit'])
        ->name('company.job-listings.edit');
    Route::put('company/job-listings/{jobListing}', [JobListingController::class, 'update'])
        ->name('company.job-listings.update');
    Route::delete('company/job-listings/{jobListing}', [JobListingController::class, 'destroy'])
        ->name('company.job-listings.destroy');
});

// Public job viewing route
Route::get('jobs/{jobListing}', [JobListingController::class, 'publicShow'])
    ->name('jobs.show');
