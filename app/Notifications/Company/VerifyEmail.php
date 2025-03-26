<?php

declare(strict_types=1);

namespace App\Notifications\Company;

use App\Models\Company;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

final class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     */
    public function verificationUrl($notifiable): string
    {
        if (self::$createUrlCallback) {
            $url = call_user_func(self::$createUrlCallback, $notifiable);
            if (is_string($url)) {
                return $url;
            }
            // Fallback to default generation if callback doesn't return a string
        }

        // Ensure we have a Company instance that can provide getKey and getEmailForVerification
        if (! $notifiable instanceof Company) {
            // Fallback URL or throw exception if needed
            return URL::route('company.verification.notice');
        }

        // Since Laravel 12 might use a different config path, use the value or default to 60
        $expireMinutes = 60;
        if (Config::has('auth.verification.expire')) {
            $configValue = Config::get('auth.verification.expire');
            if (is_int($configValue) || is_float($configValue)) {
                $expireMinutes = $configValue;
            }
        }

        $id = $notifiable->getKey();
        $email = $notifiable->getEmailForVerification();

        // Use sha1 which is Laravel's standard approach for email verification
        $hash = sha1($email);

        return URL::temporarySignedRoute(
            'company.verification.verify',
            Carbon::now()->addMinutes($expireMinutes),
            [
                'id' => $id,
                'hash' => $hash,
            ]
        );
    }

    /**
     * Get the verify email notification mail message for the given URL.
     *
     * @param  string  $url
     */
    public function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Company Email Address')
            ->line('Please click the button below to verify your company email address.')
            ->action('Verify Email Address', $url)
            ->line('If you did not create a company account, no further action is required.');
    }
}
