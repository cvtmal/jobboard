<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Settings;

use App\Http\Requests\Company\Settings\UpdateCompanyPasswordRequest;
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
        
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}
