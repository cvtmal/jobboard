<?php

declare(strict_types=1);

use App\Models\Company;
use Database\Factories\CompanyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

test('logged out company cannot access company dashboard', function () {
    // Attempt to access the company dashboard without being logged in
    $response = $this->get(route('company.dashboard'));

    // Should be redirected to the login page
    $response->assertStatus(302); // 302 is redirect status
    $response->assertRedirect('/login'); // Default Laravel auth redirects to /login
});

test('logged out company user is redirected when accessing authenticated routes', function () {
    // Get the company dashboard route which requires company authentication
    $response = $this->get(route('company.dashboard'));

    // Assert that the user is redirected
    $response->assertStatus(302);

    // Check that we're redirected away (don't check exact URL as it might be configurable)
    $response->assertRedirect();
});

test('logged in company can access company dashboard', function () {
    // Create and login as a company
    $company = CompanyFactory::new()->create([
        'email_verified_at' => now(), // Ensure the company is verified
    ]);

    $response = $this->actingAs($company, 'company')
        ->get(route('company.dashboard'));

    // Should be able to access the page
    $response->assertStatus(200);
});

test('company is properly logged out when using logout endpoint', function () {
    // Create and login as a company
    $company = CompanyFactory::new()->create([
        'email_verified_at' => now(), // Ensure the company is verified
    ]);

    // Login
    $this->actingAs($company, 'company');

    // Verify we're logged in
    $this->get(route('company.dashboard'))->assertStatus(200);

    // Logout
    $response = $this->post(route('company.logout'));

    // Should be redirected
    $response->assertRedirect();

    // Verify we're logged out - should redirect
    $this->get(route('company.dashboard'))->assertRedirect();
});

test('inactive or blocked company cannot login', function () {
    // Create an inactive company
    $inactiveCompany = CompanyFactory::new()->create([
        'active' => false,
        'email_verified_at' => now(),
    ]);

    // Try to login with inactive company
    $response = $this->post(route('company.login'), [
        'email' => $inactiveCompany->email,
        'password' => 'password', // Default password from factory
    ]);

    // Authentication should fail and redirect back
    $response->assertStatus(302);

    // Should not be authenticated
    $this->assertGuest('company');

    // Create a blocked company
    $blockedCompany = CompanyFactory::new()->create([
        'blocked' => true,
        'email_verified_at' => now(),
    ]);

    // Try to login with blocked company
    $response = $this->post(route('company.login'), [
        'email' => $blockedCompany->email,
        'password' => 'password', // Default password from factory
    ]);

    // Authentication should fail and redirect back
    $response->assertStatus(302);

    // Should not be authenticated
    $this->assertGuest('company');
});
