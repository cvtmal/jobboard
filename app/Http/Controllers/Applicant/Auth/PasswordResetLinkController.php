<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use App\Http\Requests\Applicant\Auth\PasswordResetLinkRequest;
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
        return Inertia::render('applicant/auth/forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(PasswordResetLinkRequest $request): RedirectResponse
    {
        $status = Password::broker('applicants')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
