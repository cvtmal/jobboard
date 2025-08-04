<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EmailVerificationNotificationController
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user('applicant')->hasVerifiedEmail()) { 
            return redirect()->intended(route('applicant.dashboard'));
        }

        $request->user('applicant')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
