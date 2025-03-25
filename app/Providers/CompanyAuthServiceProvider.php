<?php

declare(strict_types=1);

namespace App\Providers;

use App\Guards\CompanyGuard;
use App\Models\Company;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

final class CompanyAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register custom guard
        Auth::resolved(function (AuthManager $auth): void {
            $auth->extend('company', function (Application $app, string $name, array $config) use ($auth) {
                $provider = $auth->createUserProvider($config['provider'] ?? null);

                $guard = new CompanyGuard(
                    name: $name,
                    provider: $provider,
                    session: $app->make('session.store'),
                    request: $app->make('request')
                );

                $guard->setCookieJar($app->make('cookie'));

                return $guard;
            });

            // Register company provider
            $auth->provider('company', function (Application $app, array $config) {
                return new CompanyUserProvider($app->make('hash'));
            });
        });
    }
}
