<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class EmailVerificationPromptController
{
    /**
     * Show the email verification prompt page for companies.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        return $request->user('company')->hasVerifiedEmail() // @phpstan-ignore-line
                    ? redirect()->intended(route('company.dashboard', absolute: false))
                    : Inertia::render('company/auth/verify-email', ['status' => $request->session()->get('status')]);
    }
}
