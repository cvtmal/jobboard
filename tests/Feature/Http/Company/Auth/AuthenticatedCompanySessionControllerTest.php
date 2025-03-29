<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

it('displays the company login page', function () {
    $this->get(route('company.login'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('company/auth/login')
            ->has('canResetPassword')
            ->has('status')
        );
});

it('authenticates a company with valid credentials', function () {
    // Create a company
    $company = Company::factory()->create([
        'email' => 'test-company@example.com',
        'password' => bcrypt('password'),
    ]);

    // Attempt to login
    $response = $this->post(route('company.login'), [
        'email' => 'test-company@example.com',
        'password' => 'password',
        'remember' => true,
    ]);

    // Assert redirected to dashboard
    $response->assertRedirect(route('company.dashboard'));

    // Assert company is authenticated
    $this->assertTrue(Auth::guard('company')->check());
    $this->assertEquals($company->id, Auth::guard('company')->id());

    // Check for remember token (cookie should be set)
    $this->assertNotNull($company->fresh()->remember_token);
});

it('rejects company login with invalid credentials', function () {
    // Create a company
    Company::factory()->create([
        'email' => 'test-company@example.com',
        'password' => bcrypt('password'),
    ]);

    // Attempt to login with wrong password
    $response = $this->post(route('company.login'), [
        'email' => 'test-company@example.com',
        'password' => 'wrong-password',
    ]);

    // Assert validation error
    $response->assertSessionHasErrors(['email']);

    // Assert not authenticated
    $this->assertFalse(Auth::guard('company')->check());
});

it('logs a company out when requested', function () {
    // Create and login a company
    $company = Company::factory()->create();
    Auth::guard('company')->login($company);

    // Assert company is authenticated
    $this->assertTrue(Auth::guard('company')->check());

    // Logout
    $response = $this->post(route('company.logout'));

    // Assert redirected to login
    $response->assertRedirect(route('company.login'));

    // Assert company is no longer authenticated
    $this->assertFalse(Auth::guard('company')->check());
});
