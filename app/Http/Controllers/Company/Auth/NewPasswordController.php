<?php

declare(strict_types=1);

namespace App\Http\Controllers\Company\Auth;

use App\Http\Requests\Company\Auth\NewPasswordRequest;
use App\Models\Company;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class NewPasswordController
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('company/auth/reset-password', [
            'email' => $request->input('email'),
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(NewPasswordRequest $request): RedirectResponse
    {
        // Here we will attempt to reset the company's password. If it is successful we
        // will update the password on an actual company model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::broker('companies')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (CanResetPassword $company, string $password): void {
                if (! $company instanceof Company) {
                    return;
                }

                $company->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($company));
            }
        );

        // If the password was successfully reset, we will redirect the company back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('company.login')
                ->with('status', Lang::get($status));
        }

        // Handle error case - convert status to a proper message key
        $errorMessage = Lang::get(is_string($status) ? $status : 'passwords.user');

        throw ValidationException::withMessages([
            'email' => [$errorMessage],
        ]);
    }
}
