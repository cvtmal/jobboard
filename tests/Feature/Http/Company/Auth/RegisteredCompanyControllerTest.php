<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('displays the company registration page', function () {
    $this->get(route('company.register'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('company/auth/register')
        );
})->group('vite');

it('registers a new company', function () {
    $companyData = [
        'name' => 'Test Company',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'test-company@example.com',
        'phone_number' => '+1234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post(route('company.register'), $companyData)
        ->assertRedirect(route('company.dashboard'));

    // Verify company was created in the database
    $this->assertDatabaseHas('companies', [
        'name' => 'Test Company',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'test-company@example.com',
        'phone_number' => '+1234567890',
    ]);

    // Verify the company is authenticated
    expect(Auth::guard('company')->check())->toBeTrue();
});

it('prevents duplicate company email registrations', function () {
    // Create a company with a known email
    Company::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Try to register another company with the same email
    $response = $this->post(route('company.register'), [
        'name' => 'Duplicate Company',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '+1234567890',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);

    // Verify only one company with this email exists
    $this->assertDatabaseCount('companies', 1);
});
