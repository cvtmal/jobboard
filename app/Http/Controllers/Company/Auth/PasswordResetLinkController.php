<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Requests\Company\Auth\PasswordResetLinkRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

final class PasswordResetLinkController
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('company/auth/forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        // We will send the password reset link to this company. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::broker('companies')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
