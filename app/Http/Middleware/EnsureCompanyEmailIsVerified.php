<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

final class EnsureCompanyEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('company');

        // If no user is authenticated or the user is a Company but hasn't verified their email
        if (! $user || ! $user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? abort(403, 'Your company email address is not verified.')
                : Redirect::route('company.verification.notice');
        }

        return $next($request);
    }
}
