<?php

declare(strict_types=1);

use App\Http\Controllers\Company\CareerPageController;
use App\Http\Controllers\Company\CompanyOnboardingController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', WelcomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Company routes
Route::middleware(['auth:company', 'verified.company'])->group(function () {
    Route::get('company/dashboard', [CompanyOnboardingController::class, 'showDashboard'])->name('company.dashboard');

    // Company profile routes
    Route::get('company/profile', [CompanyOnboardingController::class, 'showProfileOverview'])->name('company.profile');
    Route::get('company/details', [CompanyOnboardingController::class, 'showProfile'])->name('company.details');
    Route::patch('company/details', [CompanyOnboardingController::class, 'updateProfile'])->name('company.details.update');

    // Career page routes
    Route::get('company/career-page', [CareerPageController::class, 'edit'])->name('company.career-page.edit');
    Route::post('company/career-page', [CareerPageController::class, 'update'])->name('company.career-page.update');
    Route::delete('company/career-page/image', [CareerPageController::class, 'destroyImage'])->name('company.career-page.image.destroy');
    Route::post('company/career-page/videos', [CareerPageController::class, 'addVideo'])->name('company.career-page.videos.store');
    Route::delete('company/career-page/videos/{videoId}', [CareerPageController::class, 'removeVideo'])->name('company.career-page.videos.destroy');
    Route::get('company/career-page/preview', [CareerPageController::class, 'preview'])->name('company.career-page.preview');
});

// Applicant routes
Route::middleware(['auth:applicant', 'verified.applicant'])->group(function () {
    Route::get('applicant/dashboard', function () {
        return Inertia::render('applicant/dashboard');
    })->name('applicant.dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/company_auth.php';
require __DIR__.'/applicant_auth.php';
require __DIR__.'/company_settings.php';
require __DIR__.'/job_listings.php';
