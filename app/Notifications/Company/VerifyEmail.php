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

        $expireMinutes = Config::get('auth.verification.expire', 60);
        if (! is_int($expireMinutes) && ! is_float($expireMinutes)) {
            $expireMinutes = 60; // Default to 60 minutes if config is invalid
        }

        $id = $notifiable->getKey();
        $email = $notifiable->getEmailForVerification();

        // Get the app key for HMAC, with a fallback if not set
        $appKey = Config::get('app.key');
        $secretKey = is_string($appKey) ? $appKey : 'laravel-secure-key';

        // Use hash_hmac with SHA-256 as per security best practices
        $hash = hash_hmac('sha256', $email, $secretKey);

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
        $subject = Lang::get('Verify Company Email Address');
        $actionText = Lang::get('Verify Email Address');

        // Ensure we have string values
        $subjectString = is_string($subject) ? $subject : 'Verify Company Email Address';
        $actionString = is_string($actionText) ? $actionText : 'Verify Email Address';

        return (new MailMessage)
            ->subject($subjectString)
            ->line(Lang::get('Please click the button below to verify your company email address.'))
            ->action($actionString, $url)
            ->line(Lang::get('If you did not create a company account, no further action is required.'));
    }
}
