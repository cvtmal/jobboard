<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EmailVerificationNotificationController
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user('company')->hasVerifiedEmail()) { // @phpstan-ignore-line
            return redirect()->intended(route('company.dashboard', absolute: false));
        }

        $request->user('company')->sendEmailVerificationNotification(); // @phpstan-ignore-line

        return back()->with('status', 'verification-link-sent');
    }
}
