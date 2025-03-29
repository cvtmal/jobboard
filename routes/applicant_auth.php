<?php

declare(strict_types=1);

use App\Http\Controllers\Applicant\Auth\AuthenticatedApplicantSessionController;
use App\Http\Controllers\Applicant\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Applicant\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Applicant\Auth\NewPasswordController;
use App\Http\Controllers\Applicant\Auth\PasswordResetLinkController;
use App\Http\Controllers\Applicant\Auth\RegisteredApplicantController;
use App\Http\Controllers\Applicant\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:applicant')->group(function () {
    Route::get('applicant/register', [RegisteredApplicantController::class, 'create'])
        ->name('applicant.register');

    Route::post('applicant/register', [RegisteredApplicantController::class, 'store']);

    Route::get('applicant/login', [AuthenticatedApplicantSessionController::class, 'create'])
        ->name('applicant.login');

    Route::post('applicant/login', [AuthenticatedApplicantSessionController::class, 'store']);

    // Password Reset
    Route::get('applicant/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('applicant.password.request');

    Route::post('applicant/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('applicant.password.email');

    Route::get('applicant/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('applicant.password.reset');

    Route::post('applicant/reset-password', [NewPasswordController::class, 'store'])
        ->name('applicant.password.store');
});

Route::middleware('auth:applicant')->group(function () {
    Route::post('applicant/logout', [AuthenticatedApplicantSessionController::class, 'destroy'])
        ->name('applicant.logout');

    Route::get('applicant/verify-email', EmailVerificationPromptController::class)
        ->name('applicant.verification.notice');

    Route::get('applicant/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('applicant.verification.verify');

    Route::post('applicant/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('applicant.verification.send');
});
