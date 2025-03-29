<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('it redirects if email is already verified', function () {
    // Create a company with a verified email
    $company = Company::factory()->create([
        'email_verified_at' => now(),
    ]);

    // Generate a valid verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'company.verification.verify',
        now()->addMinutes(60),
        ['id' => $company->id, 'hash' => sha1($company->email)]
    );

    // Make the request as the authenticated company
    $response = $this->actingAs($company, 'company')->get($verificationUrl);

    // Verify we are redirected to the company dashboard with the verified parameter
    $response->assertRedirect(route('company.dashboard').'?verified=1');

    // Verify the Verified event is not dispatched
    Event::fake();
    $this->actingAs($company, 'company')->get($verificationUrl);
    Event::assertNotDispatched(Verified::class);
});

test('it verifies company email for unverified company', function () {
    // Create a company with an unverified email
    $company = Company::factory()->create([
        'email_verified_at' => null,
    ]);

    // Fake events to detect Verified event
    Event::fake();

    // Generate a valid verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'company.verification.verify',
        now()->addMinutes(60),
        ['id' => $company->id, 'hash' => sha1($company->email)]
    );

    // Make the request as the authenticated company
    $response = $this->actingAs($company, 'company')->get($verificationUrl);

    // Verify we are redirected to the company dashboard with the verified parameter
    $response->assertRedirect(route('company.dashboard').'?verified=1');

    // Verify the email has been marked as verified
    expect($company->fresh()->hasVerifiedEmail())->toBeTrue();

    // Verify the Verified event is dispatched
    Event::assertDispatched(Verified::class, function ($event) use ($company) {
        return $event->user->id === $company->id;
    });
});

test('it fails with invalid verification URL', function () {
    // Create a company with an unverified email
    $company = Company::factory()->create([
        'email_verified_at' => null,
    ]);

    // Generate an invalid verification URL with incorrect hash
    $invalidUrl = URL::temporarySignedRoute(
        'company.verification.verify',
        now()->addMinutes(60),
        ['id' => $company->id, 'hash' => 'invalid-hash']
    );

    // Make the request as the authenticated company
    $response = $this->actingAs($company, 'company')->get($invalidUrl);

    // Request should fail with 403 status
    $response->assertForbidden();

    // Verify the email has not been marked as verified
    expect($company->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('it fails with expired verification URL', function () {
    // Create a company with an unverified email
    $company = Company::factory()->create([
        'email_verified_at' => null,
    ]);

    // Generate an expired verification URL
    $expiredUrl = URL::temporarySignedRoute(
        'company.verification.verify',
        now()->subMinutes(60), // Expired time
        ['id' => $company->id, 'hash' => sha1($company->email)]
    );

    // Make the request as the authenticated company
    $response = $this->actingAs($company, 'company')->get($expiredUrl);

    // Request should fail with 403 status (expired signature)
    $response->assertForbidden();

    // Verify the email has not been marked as verified
    expect($company->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('it requires authentication', function () {
    // Create a company with an unverified email
    $company = Company::factory()->create([
        'email_verified_at' => null,
    ]);

    // Generate a valid verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'company.verification.verify',
        now()->addMinutes(60),
        ['id' => $company->id, 'hash' => sha1($company->email)]
    );

    // Make the request without authentication
    $response = $this->get($verificationUrl);

    // Check for redirection to login (the actual path may vary based on the application routes)
    $response->assertRedirect();

    // Verify the email has not been marked as verified
    expect($company->fresh()->hasVerifiedEmail())->toBeFalse();
});
