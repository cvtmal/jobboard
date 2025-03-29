<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the locale is set in the URL, use that
        if ($request->has('locale') && in_array($request->locale, ['en', 'de'])) {
            Session::put('locale', $request->locale);
            App::setLocale($request->locale);
        } 
        // Otherwise, try to get it from the session
        else if (Session::has('locale') && in_array(Session::get('locale'), ['en', 'de'])) {
            App::setLocale(Session::get('locale'));
        }
        
        return $next($request);
    }
}
