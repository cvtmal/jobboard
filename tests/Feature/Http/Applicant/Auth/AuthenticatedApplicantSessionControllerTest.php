<?php

declare(strict_types=1);

use App\Models\Applicant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('displays the login page', function () {
    $response = $this->get(route('applicant.login'));

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->has('canResetPassword')
        ->has('status')
    );
});

it('logs in an applicant with valid credentials', function () {
    // Create an applicant with known credentials
    $applicant = Applicant::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('applicant.login'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    // Verify applicant is authenticated
    $this->assertAuthenticated('applicant');

    // Verify we're redirected to the dashboard
    $response->assertRedirect(route('applicant.dashboard'));
});

it('rejects login with invalid credentials', function () {
    // Create an applicant with known credentials
    Applicant::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Attempt login with wrong password
    $response = $this->post(route('applicant.login'), [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
        'remember' => false,
    ]);

    // Verify authentication failed
    $this->assertGuest('applicant');

    // Verify we get validation error
    $response->assertSessionHasErrors(['email']);
});

it('rejects login for applicant that does not exist', function () {
    $response = $this->post(route('applicant.login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    // Verify authentication failed
    $this->assertGuest('applicant');

    // Verify we get validation error
    $response->assertSessionHasErrors(['email']);
});

it('logs out an authenticated applicant', function () {
    // Create and login as an applicant
    $applicant = Applicant::factory()->create();
    $this->actingAs($applicant, 'applicant');

    // Verify applicant is authenticated
    $this->assertAuthenticated('applicant');

    // Perform logout
    $response = $this->post(route('applicant.logout'));

    // Verify applicant is now a guest
    $this->assertGuest('applicant');

    // Verify redirect back to login page
    $response->assertRedirect(route('applicant.login'));
});
