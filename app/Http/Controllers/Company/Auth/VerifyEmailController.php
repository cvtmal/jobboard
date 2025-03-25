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
        if (! hash_equals((string) $request->route('id'), (string) $request->user('company')?->getKey())) { // @phpstan-ignore-line
            abort(403);
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user('company')?->getEmailForVerification() ?? ''))) { // @phpstan-ignore-line
            abort(403);
        }

        if ($request->user('company')->hasVerifiedEmail()) { // @phpstan-ignore-line
            return redirect()->intended(route('company.dashboard', absolute: false).'?verified=1');
        }

        if ($request->user('company')->markEmailAsVerified()) { // @phpstan-ignore-line
            event(new Verified($request->user('company'))); // @phpstan-ignore-line
        }

        return redirect()->intended(route('company.dashboard', absolute: false).'?verified=1');
    }
}
