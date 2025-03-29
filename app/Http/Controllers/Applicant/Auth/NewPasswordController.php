<?php

declare(strict_types=1);

namespace App\Http\Controllers\Applicant\Auth;

use App\Http\Requests\Applicant\Auth\NewPasswordRequest;
use App\Models\Applicant;
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
        return Inertia::render('applicant/auth/reset-password', [
            'email' => $request->string('email'),
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
        $status = Password::broker('applicants')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (CanResetPassword $applicant, string $password): void {
                if (! $applicant instanceof Applicant) {
                    return;
                }

                $applicant->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($applicant));
            }
        );

        // If the password was successfully reset, we will redirect the applicant back to
        // the application's login view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('applicant.login')
                ->with('status', Lang::get($status));
        }

        // Handle error case - convert status to a proper message key
        $errorMessage = Lang::get(is_string($status) ? $status : 'passwords.user');

        throw ValidationException::withMessages([
            'email' => [$errorMessage],
        ]);
    }
}
