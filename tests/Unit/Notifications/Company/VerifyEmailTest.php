<?php

declare(strict_types=1);

use App\Models\Company;
use App\Notifications\Company\VerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

it('extends the base verify email notification', function () {
    $notification = new VerifyEmail();

    expect($notification)->toBeInstanceOf(BaseVerifyEmail::class);
});

it('builds a mail message with proper content', function () {
    $notification = new VerifyEmail();

    $mailMessage = $notification->buildMailMessage('https://example.com/verify');

    expect($mailMessage->subject)->toBe('Verify Company Email Address')
        ->and($mailMessage->introLines[0])->toBe('Please click the button below to verify your company email address.')
        ->and($mailMessage->actionText)->toBe('Verify Email Address')
        ->and($mailMessage->actionUrl)->toBe('https://example.com/verify')
        ->and($mailMessage->outroLines[0])->toBe('If you did not create a company account, no further action is required.');
});

it('generates a proper verification url', function () {
    Config::set('auth.verification.expire', 60);

    $company = Company::factory()->create([
        'email' => 'test@example.com',
    ]);

    $notification = new VerifyEmail();

    $verificationUrl = $notification->verificationUrl($company);

    expect($verificationUrl)->toBeString()
        ->and($verificationUrl)->toContain('company/verify-email/'.$company->id)
        ->and($verificationUrl)->toContain('/'.sha1('test@example.com'))
        ->and($verificationUrl)->toContain('expires=')
        ->and($verificationUrl)->toContain('signature=');
});
