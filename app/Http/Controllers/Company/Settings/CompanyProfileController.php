<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Settings;

use App\Http\Requests\Company\Settings\CompanyProfileUpdateRequest;
use App\Models\Company;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class CompanyProfileController
{
    /**
     * Show the company's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('company/settings/profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the company's profile settings.
     */
    public function update(CompanyProfileUpdateRequest $request): RedirectResponse
    {
        $company = $request->user();

        // Ensure we have a valid company user
        if (! $company instanceof Company) {
            return back()->withErrors(['general' => 'Invalid company account']);
        }

        $company->fill($request->validated());

        if ($company->isDirty('email')) {
            $company->email_verified_at = null;
        }

        $company->save();

        return to_route('company.settings.profile');
    }

    /**
     * Delete the company's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $company = $request->user();

        // Ensure we have a valid company user
        if (! $company instanceof Company) {
            return back()->withErrors(['general' => 'Invalid company account']);
        }

        Auth::guard('company')->logout();

        $company->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
