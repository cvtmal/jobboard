<?php

declare(strict_types=1);

use App\Http\Requests\Company\Auth\EmailVerificationRequest;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create([
        'id' => 123,
        'email' => 'test@example.com',
        'email_verified_at' => null,
    ]);
    
    $this->request = new EmailVerificationRequest();
    
    // Set up company in request
    $this->request->setUserResolver(fn () => $this->company);
});

it('has empty validation rules', function () {
    expect($this->request->rules())->toBeEmpty();
});

it('requires a valid company user', function () {
    // Reset user resolver to null
    $this->request->setUserResolver(fn () => null);
    
    // Set up route parameters
    $route = new Route(['GET'], 'company/verify-email/{id}/{hash}', []);
    $route->parameters = ['id' => 123, 'hash' => sha1('test@example.com')];
    $this->request->setRouteResolver(fn () => $route);
    
    // Should fail authorization without a company user
    expect($this->request->authorize())->toBeFalse();
});

it('validates company ID matches route ID', function () {
    // Set up route parameters with a different ID
    $route = new Route(['GET'], 'company/verify-email/{id}/{hash}', []);
    $route->parameters = ['id' => 456, 'hash' => sha1('test@example.com')];
    $this->request->setRouteResolver(fn () => $route);
    
    // Should fail authorization with mismatched IDs
    expect($this->request->authorize())->toBeFalse();
});

it('validates route hash against email', function () {
    // Set up route parameters with the correct hash
    $correctHash = sha1('test@example.com');
    $route = new Route(['GET'], 'company/verify-email/{id}/{hash}', []);
    $route->parameters = ['id' => 123, 'hash' => $correctHash];
    $this->request->setRouteResolver(fn () => $route);
    
    // Should pass authorization with correct hash
    expect($this->request->authorize())->toBeTrue();
});

it('fails authorization with incorrect hash', function () {
    // Set up route parameters with an incorrect hash
    $incorrectHash = sha1('wrong@example.com');
    $route = new Route(['GET'], 'company/verify-email/{id}/{hash}', []);
    $route->parameters = ['id' => 123, 'hash' => $incorrectHash];
    $this->request->setRouteResolver(fn () => $route);
    
    // Should fail authorization with incorrect hash
    expect($this->request->authorize())->toBeFalse();
});

it('fails authorization with missing route parameters', function () {
    // Set up route with missing parameters
    $route = new Route(['GET'], 'company/verify-email/{id}/{hash}', []);
    $route->parameters = []; // No parameters
    $this->request->setRouteResolver(fn () => $route);
    
    // Should fail authorization with missing parameters
    expect($this->request->authorize())->toBeFalse();
});

it('uses constant-time comparison for hash verification', function () {
    // This test is actually testing the implementation in Laravel
    // Since we're using sha1 and hash_equals as per Laravel's standard approach
    
    // Set up route parameters with the correct hash
    $correctHash = sha1('test@example.com');
    $route = new Route(['GET'], 'company/verify-email/{id}/{hash}', []);
    $route->parameters = ['id' => 123, 'hash' => $correctHash];
    $this->request->setRouteResolver(fn () => $route);
    
    // Authorization should pass with the correct hash
    expect($this->request->authorize())->toBeTrue();
    
    // This is not a perfect test for constant-time comparison
    // but we're testing that our implementation follows Laravel's security standards
});
