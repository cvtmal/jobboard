<?php

declare(strict_types=1);

use App\Actions\Company\CreateCompanyAction;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('displays the company registration page', function () {
    $this->get(route('company.register'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => 
            $page->component('company/auth/register')
        );
});

it('registers a new company', function () {
    $companyData = [
        'name' => 'Test Company',
        'email' => 'test-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'address' => '123 Test Street',
        'postcode' => '12345',
        'city' => 'Test City',
        'url' => 'https://testcompany.com',
    ];

    $this->post(route('company.register'), $companyData)
        ->assertRedirect(route('company.dashboard'));

    // Verify company was created in the database
    $this->assertDatabaseHas('companies', [
        'name' => 'Test Company',
        'email' => 'test-company@example.com',
        'address' => '123 Test Street',
        'postcode' => '12345',
        'city' => 'Test City',
        'url' => 'https://testcompany.com',
        'active' => true,
        'blocked' => false,
    ]);

    // Verify the company is authenticated
    expect(Auth::guard('company')->check())->toBeTrue();
});

it('validates company registration data', function () {
    // Missing required fields
    $response = $this->post(route('company.register'), [
        'name' => '',
        'email' => '',
        'password' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'password']);

    // Invalid email
    $response = $this->post(route('company.register'), [
        'name' => 'Test Company',
        'email' => 'not-an-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);

    // Password confirmation mismatch
    $response = $this->post(route('company.register'), [
        'name' => 'Test Company',
        'email' => 'test-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertSessionHasErrors(['password']);

    // Invalid URL
    $response = $this->post(route('company.register'), [
        'name' => 'Test Company',
        'email' => 'test-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'url' => 'not-a-valid-url',
    ]);

    $response->assertSessionHasErrors(['url']);
});

it('prevents duplicate company email registrations', function () {
    // Create a company with a known email
    Company::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Try to register another company with the same email
    $response = $this->post(route('company.register'), [
        'name' => 'Duplicate Company',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);

    // Verify only one company with this email exists
    $this->assertDatabaseCount('companies', 1);
});
