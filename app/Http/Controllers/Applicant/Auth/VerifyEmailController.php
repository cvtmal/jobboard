<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use App\Http\Requests\Applicant\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;

final class VerifyEmailController
{
    /**
     * Mark the authenticated applicant's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user('applicant')->hasVerifiedEmail()) {
            return redirect()->intended(route('applicant.dashboard', absolute: false).'?verified=1');
        }

        if ($request->user('applicant')->markEmailAsVerified()) {
            event(new Verified($request->user('applicant')));
        }

        return redirect()->intended(route('applicant.dashboard', absolute: false).'?verified=1');
    }
}
