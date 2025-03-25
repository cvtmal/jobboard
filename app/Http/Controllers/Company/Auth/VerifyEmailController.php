<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class VerifyEmailController
{
    /**
     * Mark the authenticated company's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // Verify that the request id matches the authenticated company id
        if (! hash_equals((string) $request->route('id'), (string) $request->user('company')?->getKey())) {
            abort(403);
        }

        // Verify that the hash matches the company's email
        if (! hash_equals((string) $request->route('hash'), sha1($request->user('company')?->getEmailForVerification() ?? ''))) {
            abort(403);
        }

        // If already verified, redirect to dashboard
        if ($request->user('company')->hasVerifiedEmail()) {
            return redirect()->intended(route('company.dashboard', absolute: false).'?verified=1');
        }

        // Mark email as verified and fire event
        if ($request->user('company')->markEmailAsVerified()) {
            event(new Verified($request->user('company')));
        }

        return redirect()->intended(route('company.dashboard', absolute: false).'?verified=1');
    }
}
