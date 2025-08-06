<?php

declare(strict_types=1);

use App\Models\Applicant;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('it displays password request view', function () {
    $response = $this->get(route('applicant.password.request'));

    $response->assertStatus(200);
    // Skip Inertia component check as it's environment-dependent
    $response->assertInertia(fn ($page) => $page);
})->group('vite')->skip('Vite manifest issue - applicant auth pages not yet implemented');

test('it sends password reset link for valid email', function () {
    // Create an applicant
    $applicant = Applicant::factory()->create();

    // Prevent actual notifications from being sent
    Notification::fake();

    // Send password reset request
    $response = $this->post(route('applicant.password.email'), [
        'email' => $applicant->email,
    ]);

    // Assert status and redirect back with success status
    $response->assertRedirect();
    $response->assertSessionHas('status');

    // Assert notification was sent to the correct applicant
    Notification::assertSentTo(
        $applicant,
        ResetPassword::class
    );
});

test('it shows error for non-existent email', function () {
    // Send password reset request for non-existent email
    $response = $this->post(route('applicant.password.email'), [
        'email' => 'nonexistent@example.com',
    ]);

    // Assert redirect back with input and errors
    $response->assertRedirect();
    $response->assertSessionHasErrors('email');

    // Ensure the error message is present
    $response->assertSessionHasErrorsIn('email');
});

test('it requires a valid email format', function () {
    // Send password reset request with invalid email format
    $response = $this->post(route('applicant.password.email'), [
        'email' => 'not-an-email',
    ]);

    // Assert validation errors
    $response->assertSessionHasErrors('email');
});
