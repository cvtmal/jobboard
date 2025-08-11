<?php

declare(strict_types=1);

use App\Models\Company;
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
