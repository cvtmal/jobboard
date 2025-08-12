<?php

declare(strict_types=1);

use App\Http\Controllers\Company\JobListingController as CompanyJobListingController;
use App\Http\Controllers\Company\JobListingImageController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\PublicJobListingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('job-listings', JobListingController::class);
});

// Company-specific job listing routes
Route::middleware(['auth:company', 'verified.company'])->group(function () {
    Route::get('company/job-listings', [CompanyJobListingController::class, 'index'])->name('company.job-listings.index');
    Route::get('company/job-listings/create', [CompanyJobListingController::class, 'create'])->name('company.job-listings.create');
    Route::post('company/job-listings', [CompanyJobListingController::class, 'store'])->name('company.job-listings.store');
    Route::post('company/job-listings/with-subscription', [CompanyJobListingController::class, 'storeWithSubscription'])->name('company.job-listings.store-with-subscription');
    Route::get('company/job-listings/{jobListing}/screening', [CompanyJobListingController::class, 'editScreening'])->name('company.job-listings.screening');
    Route::post('company/job-listings/{jobListing}/screening', [CompanyJobListingController::class, 'updateScreening'])->name('company.job-listings.screening.update');
    Route::get('company/job-listings/{jobListing}', [CompanyJobListingController::class, 'show'])->name('company.job-listings.show');
    Route::get('company/job-listings/{jobListing}/edit', [CompanyJobListingController::class, 'edit'])->name('company.job-listings.edit');
    Route::post('company/job-listings/{jobListing}', [CompanyJobListingController::class, 'update'])->name('company.job-listings.update');
    Route::post('company/job-listings/{jobListing}/with-subscription', [CompanyJobListingController::class, 'updateWithSubscription'])->name('company.job-listings.update-with-subscription');
    Route::delete('company/job-listings/{jobListing}', [CompanyJobListingController::class, 'destroy'])->name('company.job-listings.destroy');

    // Job listing image management routes
    Route::prefix('company/job-listings/{jobListing}/images')->name('company.job-listings.images.')->group(function () {
        Route::get('/', [JobListingImageController::class, 'show'])->name('show');

        Route::post('logo', [JobListingImageController::class, 'uploadLogo'])->name('logo.upload');
        Route::delete('logo', [JobListingImageController::class, 'deleteLogo'])->name('logo.delete');

        Route::post('banner', [JobListingImageController::class, 'uploadBanner'])->name('banner.upload');
        Route::delete('banner', [JobListingImageController::class, 'deleteBanner'])->name('banner.delete');

        Route::patch('toggle', [JobListingImageController::class, 'toggleCompanyImages'])->name('toggle');
    });
});

// Public job viewing route
Route::get('jobs/{jobListing}', [PublicJobListingController::class, 'show'])
    ->name('jobs.show');
