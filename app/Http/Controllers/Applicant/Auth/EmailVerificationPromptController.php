<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class EmailVerificationPromptController
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user('applicant')->hasVerifiedEmail()
            ? redirect()->intended(route('applicant.dashboard'))
            : Inertia::render('applicant/auth/verify-email');
    }
}
