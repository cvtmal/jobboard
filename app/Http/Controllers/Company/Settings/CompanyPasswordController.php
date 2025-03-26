<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Settings;

use App\Http\Requests\Company\Settings\UpdateCompanyPasswordRequest;
use App\Models\Company;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

final class CompanyPasswordController
{
    /**
     * Show the company's password settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('company/settings/password', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the company's password.
     */
    public function update(UpdateCompanyPasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Ensure we have a valid user object of Company type
        if (! $user instanceof Company) {
            return back()->withErrors(['general' => 'Invalid user account']);
        }

        // Ensure password is a string
        $password = $validated['password'] ?? '';
        if (! is_string($password)) {
            return back()->withErrors(['password' => 'Invalid password format']);
        }

        $user->update([
            'password' => Hash::make($password),
        ]);

        return back();
    }
}
