<?php

declare(strict_types=1);

use App\Http\Controllers\Company\Auth\AuthenticatedCompanySessionController;
use App\Http\Controllers\Company\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Company\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Company\Auth\NewPasswordController;
use App\Http\Controllers\Company\Auth\PasswordResetLinkController;
use App\Http\Controllers\Company\Auth\RegisteredCompanyController;
use App\Http\Controllers\Company\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:company')->group(function () {
    Route::get('company/register', [RegisteredCompanyController::class, 'create'])
        ->name('company.register');

    Route::post('company/register', [RegisteredCompanyController::class, 'store']);

    Route::get('company/login', [AuthenticatedCompanySessionController::class, 'create'])
        ->name('company.login');

    Route::post('company/login', [AuthenticatedCompanySessionController::class, 'store']);

    // Password Reset
    Route::get('company/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('company.password.request');

    Route::post('company/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('company.password.email');

    Route::get('company/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('company.password.reset');

    Route::post('company/reset-password', [NewPasswordController::class, 'store'])
        ->name('company.password.store');
});

Route::middleware('auth:company')->group(function () {
    Route::post('company/logout', [AuthenticatedCompanySessionController::class, 'destroy'])
        ->name('company.logout');

    Route::get('company/verify-email', EmailVerificationPromptController::class)
        ->name('company.verification.notice');

    Route::get('company/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('company.verification.verify');

    Route::post('company/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('company.verification.send');
});
