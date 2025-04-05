<?php

declare(strict_types=1);

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
    Route::get('company/dashboard', function () {
        return Inertia::render('company/dashboard');
    })->name('company.dashboard');

    Route::get('company/onboarding', function () {
        return Inertia::render('company/onboarding');
    })->name('company.onboarding');
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
