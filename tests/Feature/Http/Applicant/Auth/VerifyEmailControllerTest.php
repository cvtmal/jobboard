<?php

declare(strict_types=1);

use App\Models\Applicant;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

it('redirects to dashboard if email already verified', function () {
    // Create verified applicant
    $applicant = Applicant::factory()->create([
        'email_verified_at' => now(),
    ]);

    // Login as applicant
    $this->actingAs($applicant, 'applicant');

    // Generate a verification URL (which shouldn't be used but needs to be valid)
    $verificationUrl = URL::temporarySignedRoute(
        'applicant.verification.verify',
        now()->addMinutes(60),
        [
            'id' => $applicant->id,
            'hash' => sha1($applicant->email),
        ]
    );

    // Access the verification URL
    $response = get($verificationUrl);

    // Verify redirect to dashboard with verified parameter
    $response->assertRedirect(route('applicant.dashboard').'?verified=1');
});

it('verifies email for unverified applicant', function () {
    // Set up event fake
    Event::fake();

    // Create unverified applicant
    $applicant = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);

    // Login as applicant
    $this->actingAs($applicant, 'applicant');

    // Generate a verification URL
    $verificationUrl = URL::temporarySignedRoute(
        'applicant.verification.verify',
        now()->addMinutes(60),
        [
            'id' => $applicant->id,
            'hash' => sha1($applicant->email),
        ]
    );

    // Access the verification URL
    $response = get($verificationUrl);

    // Check that the applicant's email is now verified
    expect($applicant->fresh()->hasVerifiedEmail())->toBeTrue();

    // Verify the Verified event was dispatched
    Event::assertDispatched(Verified::class, function ($event) use ($applicant) {
        return $event->user->id === $applicant->id;
    });

    // Verify redirect to dashboard with verified parameter
    $response->assertRedirect(route('applicant.dashboard').'?verified=1');
});

it('rejects verification with invalid hash', function () {
    // Create unverified applicant
    $applicant = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);

    // Login as applicant
    $this->actingAs($applicant, 'applicant');

    // Generate a verification URL with incorrect hash
    $verificationUrl = URL::temporarySignedRoute(
        'applicant.verification.verify',
        now()->addMinutes(60),
        [
            'id' => $applicant->id,
            'hash' => 'invalid-hash',
        ]
    );

    // Access the verification URL
    $response = get($verificationUrl);

    // Verify we get a 403 response (unauthorized)
    $response->assertForbidden();

    // Check that the applicant's email is still not verified
    expect($applicant->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('rejects verification with mismatched id', function () {
    // Create two applicants
    $applicant1 = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);

    $applicant2 = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);

    // Login as first applicant
    $this->actingAs($applicant1, 'applicant');

    // Generate a verification URL with second applicant's ID
    $verificationUrl = URL::temporarySignedRoute(
        'applicant.verification.verify',
        now()->addMinutes(60),
        [
            'id' => $applicant2->id,
            'hash' => sha1($applicant1->email),
        ]
    );

    // Access the verification URL
    $response = get($verificationUrl);

    // Verify we get a 403 response (unauthorized)
    $response->assertForbidden();

    // Check that neither applicant's email is verified
    expect($applicant1->fresh()->hasVerifiedEmail())->toBeFalse();
    expect($applicant2->fresh()->hasVerifiedEmail())->toBeFalse();
});
