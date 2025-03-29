<?php

declare(strict_types=1);

use App\Http\Requests\Applicant\Auth\EmailVerificationRequest;
use App\Models\Applicant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->applicant = Applicant::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->request = new EmailVerificationRequest();
    $this->request->setUserResolver(fn () => $this->applicant);
});

it('authorizes when all parameters are valid', function () {
    // Set up route parameters
    $hash = sha1($this->applicant->email);
    $route = new Route('GET', 'test-route', []);
    $route->parameters = ['id' => (string) $this->applicant->id, 'hash' => $hash];

    $this->request->setRouteResolver(fn () => $route);

    // The request should be authorized
    expect($this->request->authorize())->toBeTrue();
});

it('rejects when user is not logged in as applicant', function () {
    // Set up route parameters
    $hash = sha1($this->applicant->email);
    $route = new Route('GET', 'test-route', []);
    $route->parameters = ['id' => (string) $this->applicant->id, 'hash' => $hash];

    // Set null user resolver
    $this->request->setUserResolver(fn () => null);
    $this->request->setRouteResolver(fn () => $route);

    // The request should not be authorized
    expect($this->request->authorize())->toBeFalse();
});

it('rejects when route id does not match user id', function () {
    // Set up route parameters with incorrect id
    $hash = sha1($this->applicant->email);
    $route = new Route('GET', 'test-route', []);
    $route->parameters = ['id' => '99999', 'hash' => $hash];

    $this->request->setRouteResolver(fn () => $route);

    // The request should not be authorized
    expect($this->request->authorize())->toBeFalse();
});

it('rejects when hash does not match SHA-1 of email', function () {
    // Set up route parameters with incorrect hash
    $route = new Route('GET', 'test-route', []);
    $route->parameters = ['id' => (string) $this->applicant->id, 'hash' => 'invalid-hash'];

    $this->request->setRouteResolver(fn () => $route);

    // The request should not be authorized
    expect($this->request->authorize())->toBeFalse();
});

it('rejects when route parameters are missing', function () {
    // Set up route without parameters
    $route = new Route('GET', 'test-route', []);
    $route->parameters = [];

    $this->request->setRouteResolver(fn () => $route);

    // The request should not be authorized
    expect($this->request->authorize())->toBeFalse();
});

it('has no validation rules', function () {
    expect($this->request->rules())->toBeEmpty();
});
