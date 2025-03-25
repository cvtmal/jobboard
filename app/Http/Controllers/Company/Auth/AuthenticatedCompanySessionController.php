<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Requests\Company\Auth\LoginCompanyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class AuthenticatedCompanySessionController
{
    /**
     * Display the company login view.
     */
    public function create(): Response
    {
        return Inertia::render('company/auth/login');
    }

    /**
     * Handle an incoming company authentication request.
     *
     * @throws ValidationException
     */
    public function store(LoginCompanyRequest $request): RedirectResponse
    {
        if (! Auth::guard('company')->attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records or your company account may be blocked.',
            ]);
        }

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
