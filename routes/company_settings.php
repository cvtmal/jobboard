<?php

declare(strict_types=1);

use App\Http\Controllers\Company\Settings\CompanyPasswordController;
use App\Http\Controllers\Company\Settings\CompanyProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:company', 'verified.company'])->group(function () {
    Route::redirect('company/settings', 'company/settings/profile');

    Route::get('company/settings/profile', [CompanyProfileController::class, 'edit'])->name('company.settings.profile');
    Route::patch('company/settings/profile', [CompanyProfileController::class, 'update'])->name('company.profile.update');
    Route::delete('company/settings/profile', [CompanyProfileController::class, 'destroy'])->name('company.profile.destroy');

    Route::get('company/settings/password', [CompanyPasswordController::class, 'edit'])->name('company.settings.password');
    Route::put('company/settings/password', [CompanyPasswordController::class, 'update'])->name('company.password.update');

    Route::get('company/settings/appearance', function () {
        return Inertia::render('company/settings/appearance');
    })->name('company.settings.appearance');
});
