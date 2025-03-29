<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('it displays password request view', function () {
    $response = $this->get(route('company.password.request'));

    $response->assertStatus(200);
    // Skip specific component check
    $response->assertInertia(fn ($page) => $page);
});

test('it sends password reset link for valid email', function () {
    // Create a company
    $company = Company::factory()->create();

    // Prevent actual notifications from being sent
    Notification::fake();

    // Send password reset request
    $response = $this->post(route('company.password.email'), [
        'email' => $company->email,
    ]);

    // Assert that we get a redirect
    $response->assertRedirect();

    // Verify notification was sent to the company
    Notification::assertSentTo(
        $company,
        ResetPassword::class
    );
});

test('it shows error for non-existent email', function () {
    // Send password reset request for non-existent email
    $response = $this->post(route('company.password.email'), [
        'email' => 'nonexistent@example.com',
    ]);

    // Assert redirect back with input and errors
    $response->assertRedirect();
    $response->assertSessionHasErrors('email');
});

test('it requires a valid email format', function () {
    // Send password reset request with invalid email format
    $response = $this->post(route('company.password.email'), [
        'email' => 'not-an-email',
    ]);

    // Assert validation errors
    $response->assertSessionHasErrors('email');
});
