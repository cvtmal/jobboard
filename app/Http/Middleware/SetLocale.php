<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('locale')) {
            $localeInput = $request->input('locale');
            if (in_array($localeInput, ['en', 'de'], true)) {
                Session::put('locale', $localeInput);
                App::setLocale($localeInput);
            }
        } elseif (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, ['en', 'de'], true)) {
                App::setLocale($sessionLocale);
            }
        }

        return $next($request);
    }
}
