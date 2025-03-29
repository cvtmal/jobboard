<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\Providers\ApplicantUserProvider;
use App\Guards\ApplicantGuard;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

final class ApplicantAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register custom guard
        Auth::resolved(function (AuthManager $auth): void {
            $auth->extend('applicant', function (Application $app, string $name, array $config) use ($auth): ApplicantGuard {
                // Get provider name from config, ensuring it's a string or null
                $providerName = isset($config['provider']) && is_string($config['provider'])
                    ? $config['provider']
                    : 'applicant';

                // Create user provider
                $provider = $auth->createUserProvider($providerName);

                // Ensure we have a valid UserProvider
                if (! $provider instanceof UserProvider) {
                    // Fallback to creating our own provider if createUserProvider fails
                    $provider = new ApplicantUserProvider($app->make('hash'));
                }

                $guard = new ApplicantGuard(
                    name: $name,
                    provider: $provider,
                    session: $app->make('session.store'),
                    request: $app->make('request')
                );

                $guard->setCookieJar($app->make('cookie'));

                return $guard;
            });

            // Register custom user provider
            $auth->provider('applicant', fn (Application $app, array $config): ApplicantUserProvider => new ApplicantUserProvider($app->make('hash')));
        });
    }
}
