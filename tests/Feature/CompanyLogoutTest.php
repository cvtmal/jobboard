<?php

declare(strict_types=1);

use App\Models\Company;
use Database\Factories\CompanyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a verified company
    $this->company = CompanyFactory::new()->create([
        'email_verified_at' => now(),
    ]);
});

test('company cannot access protected routes after logout', function () {
    // Login as company
    $this->actingAs($this->company, 'company');
    
    // Verify we're logged in
    $response = $this->get(route('company.dashboard'));
    $response->assertStatus(200);
    
    // Logout
    $this->post(route('company.logout'));
    
    // Verify we can't access protected routes
    $response = $this->get(route('company.dashboard'));
    $response->assertStatus(302);
    $response->assertRedirect('/login');
});

test('company remember token is properly cleared after logout', function () {
    // Login with remember token
    $response = $this->post(route('company.login'), [
        'email' => $this->company->email,
        'password' => 'password', // Default factory password
        'remember' => true,
    ]);
    
    // Verify we're redirected to dashboard (successful login)
    $response->assertRedirect(route('company.dashboard'));
    
    // Verify we're authenticated
    $this->assertTrue(Auth::guard('company')->check());
    
    // Logout
    $this->post(route('company.logout'));
    
    // Verify we can't access protected routes
    $response = $this->get(route('company.dashboard'));
    $response->assertStatus(302);
    $response->assertRedirect('/login');
    
    // Verify we're not authenticated
    $this->assertFalse(Auth::guard('company')->check());
    
    // Try to access dashboard without credentials (should fail even with cookie)
    $response = $this->get(route('company.dashboard'));
    $response->assertStatus(302); // Should redirect to login
});

test('company session is properly invalidated after logout', function () {
    // Login
    $this->actingAs($this->company, 'company');
    
    // Store the session ID before logout
    $sessionId = session()->getId();
    $this->assertNotNull($sessionId);
    
    // Logout
    $this->post(route('company.logout'));
    
    // Verify session ID changed
    $this->assertNotEquals($sessionId, session()->getId());
    
    // Verify we're not authenticated
    $this->assertFalse(Auth::guard('company')->check());
});

test('manual authentication after logout works correctly', function () {
    // Login
    $this->actingAs($this->company, 'company');
    
    // Logout
    $this->post(route('company.logout'));
    
    // Verify we're logged out
    $this->assertFalse(Auth::guard('company')->check());
    
    // Login again
    $response = $this->post(route('company.login'), [
        'email' => $this->company->email,
        'password' => 'password', // Default factory password
    ]);
    
    // Verify we're logged in again
    $this->assertTrue(Auth::guard('company')->check());
    $response->assertRedirect(route('company.dashboard'));
});
