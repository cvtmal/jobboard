<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Actions\Company\CreateCompanyAction;
use App\Http\Requests\Company\Auth\RegisterCompanyRequest;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class RegisteredCompanyController
{
    /**
     * Show the company registration page.
     */
    public function create(): Response
    {
        return Inertia::render('company/auth/register');
    }

    /**
     * Handle an incoming company registration request.
     */
    public function store(RegisterCompanyRequest $request, CreateCompanyAction $action): RedirectResponse
    {
        $company = $action->execute($request->validated());

        event(new Registered($company));

        Auth::guard('company')->login($company);

        return to_route('company.dashboard');
    }
}
