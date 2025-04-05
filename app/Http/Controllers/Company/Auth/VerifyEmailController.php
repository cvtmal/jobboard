<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Requests\Company\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;

final class VerifyEmailController
{
    /**
     * Mark the authenticated company's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user('company')->hasVerifiedEmail()) { // @phpstan-ignore-line
            return redirect()->intended(route('company.onboarding', absolute: false).'?verified=1');
        }

        if ($request->user('company')->markEmailAsVerified()) { // @phpstan-ignore-line
            event(new Verified($request->user('company'))); // @phpstan-ignore-line
        }

        return redirect()->intended(route('company.onboarding', absolute: false).'?verified=1');
    }
}
