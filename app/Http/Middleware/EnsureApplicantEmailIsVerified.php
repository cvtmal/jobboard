<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureApplicantEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('applicant');

        // If no user is authenticated or the user is an Applicant but hasn't verified their email
        if (! $user || ! $user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? abort(403, 'Your applicant email address is not verified.')
                : redirect()->route('applicant.verification.notice');
        }

        return $next($request);
    }
}
