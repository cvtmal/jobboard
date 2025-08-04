<?php

declare(strict_types=1);

namespace App\Notifications\Company;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

final class VerifyEmail extends BaseVerifyEmail
{
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
