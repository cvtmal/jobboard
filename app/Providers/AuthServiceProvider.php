<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Applicant;
use App\Models\Company;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Policies are automatically discovered
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Set up dynamic email verification URL generation
        VerifyEmail::createUrlUsing(function ($notifiable): string {
            $routePrefix = match (get_class($notifiable)) {
                Applicant::class => 'applicant',
                Company::class => 'company',
                default => '',
            };

            return URL::temporarySignedRoute(
                "{$routePrefix}.verification.verify",
                Carbon::now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });
    }
}
