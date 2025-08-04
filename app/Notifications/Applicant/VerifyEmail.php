<?php

declare(strict_types=1);

namespace App\Notifications\Applicant;

use App\Models\Applicant;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
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
            ->subject('Verify Applicant Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $url)
            ->line('If you did not create an applicant account, no further action is required.');
    }
}
