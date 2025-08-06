<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company;

use App\Http\Requests\Company\Settings\CompanyProfileUpdateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CompanyOnboardingController
{
    /**
     * Show the company dashboard with onboarding guidance if needed.
     */
    public function showDashboard(Request $request): Response
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            abort(403, 'Invalid company account');
        }

        return Inertia::render('company/dashboard', [
            'company' => $company->load([]),
            'shouldShowOnboarding' => $company->shouldShowOnboarding(),
        ]);
    }

    /**
     * Show the company profile onboarding page.
     */
    public function showProfile(Request $request): Response
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            abort(403, 'Invalid company account');
        }

        return Inertia::render('company/profile', [
            'company' => $company->load([]),
            'shouldShowOnboarding' => $company->shouldShowOnboarding(),
        ]);
    }

    /**
     * Update the company profile during onboarding.
     */
    public function updateProfile(CompanyProfileUpdateRequest $request): RedirectResponse
    {
        $company = $request->user();

        if (! $company instanceof Company) {
            return back()->withErrors(['general' => 'Invalid company account']);
        }

        $validated = $request->validated();

        // Update the company profile
        $company->fill($validated);

        if ($company->isDirty('email')) {
            $company->email_verified_at = null;
        }

        $company->save();

        return back()->with('status', 'profile-updated');
    }
}
