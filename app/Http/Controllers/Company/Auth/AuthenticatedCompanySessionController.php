<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Requests\Company\Auth\LoginCompanyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class AuthenticatedCompanySessionController
{
    /**
     * Display the company login view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('company/auth/login', [
            'canResetPassword' => Route::has('company.password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming company authentication request.
     *
     * @throws ValidationException
     */
    public function store(LoginCompanyRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('company.dashboard'));
    }

    /**
     * Destroy an authenticated company session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('company.login');
    }
}
