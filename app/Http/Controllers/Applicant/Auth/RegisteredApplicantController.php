<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use App\Actions\Applicant\CreateApplicantAction;
use App\Http\Requests\Applicant\Auth\RegisterApplicantRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class RegisteredApplicantController
{
    /**
     * Show the applicant registration page.
     */
    public function create(): Response
    {
        return Inertia::render('applicant/auth/register');
    }

    /**
     * Handle an incoming applicant registration request.
     *
     * @throws Throwable
     */
    public function store(RegisterApplicantRequest $request, CreateApplicantAction $action): RedirectResponse
    {
        $applicant = $action->execute($request->validated());

        Auth::guard('applicant')->login($applicant);

        return redirect(route('applicant.verification.notice'));
    }
}
