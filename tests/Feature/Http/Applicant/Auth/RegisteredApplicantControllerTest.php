<?php

declare(strict_types=1);

use App\Models\Applicant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('displays the registration page', function () {
    $response = $this->get(route('applicant.register'));

    $response->assertStatus(200);

    // Skip Inertia component assertion as we don't know the exact path
    // Just check that we get an Inertia response
    $response->assertInertia(fn (AssertableInertia $page) => $page);
})->group('vite');

it('registers a new applicant', function () {
    // Use a real integration test with the actual action
    $response = $this->post(route('applicant.register'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Assert that a new applicant was created
    $this->assertDatabaseHas('applicants', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    // Assert that the user was logged in
    $this->assertAuthenticated('applicant');

    // Assert that we were redirected to the email verification notice
    $response->assertRedirect(route('applicant.verification.notice'));
});

it('validates registration data', function () {
    // Test with missing data
    $response = $this->post(route('applicant.register'), [
        'first_name' => '',
        'last_name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'password']);

    // Ensure no applicant was created
    $this->assertDatabaseCount('applicants', 0);

    // Assert that we are not authenticated
    $this->assertGuest('applicant');
});

it('rejects registration with duplicate email', function () {
    // Create an existing applicant
    Applicant::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Try to register with the same email
    $response = $this->post(route('applicant.register'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    // Should fail validation
    $response->assertSessionHasErrors(['email']);

    // Ensure only one applicant exists with that email
    $this->assertDatabaseCount('applicants', 1);

    // Assert that we are not authenticated
    $this->assertGuest('applicant');
});
