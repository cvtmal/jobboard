<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

final class EnsureCompanyEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user('company') ||
            ($request->user('company') instanceof MustVerifyEmail &&
            ! $request->user('company')->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your company email address is not verified.')
                : Redirect::route('company.verification.notice');
        }

        return $next($request);
    }
}
