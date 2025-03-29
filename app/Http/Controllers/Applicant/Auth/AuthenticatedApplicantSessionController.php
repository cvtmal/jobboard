<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use App\Http\Requests\Applicant\Auth\LoginApplicantRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class AuthenticatedApplicantSessionController
{
    /**
     * Display the applicant login view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('applicant/auth/login', [
            'canResetPassword' => Route::has('applicant.password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming applicant authentication request.
     *
     * @throws ValidationException
     */
    public function store(LoginApplicantRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('applicant.dashboard'));
    }

    /**
     * Destroy an authenticated applicant session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('applicant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('applicant.login');
    }
}
