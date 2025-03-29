<?php

declare(strict_types=1);

use App\Http\Requests\Company\Auth\LoginCompanyRequest;
use App\Models\Company;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->request = new LoginCompanyRequest();

    // Mock the request data
    $this->request->merge([
        'email' => 'company@example.com',
        'password' => 'password',
        'remember' => true,
    ]);

    // Set up IP for throttle key
    $this->request->server->set('REMOTE_ADDR', '127.0.0.1');
});

it('is always authorized', function () {
    expect($this->request->authorize())->toBeTrue();
});

it('has the expected validation rules', function () {
    $rules = $this->request->rules();

    expect($rules)->toHaveKey('email')
        ->and($rules['email'])->toContain('required')
        ->and($rules['email'])->toContain('email')
        ->and($rules['email'])->toContain('lowercase')
        ->and($rules)->toHaveKey('password')
        ->and($rules['password'])->toContain('required')
        ->and($rules)->toHaveKey('remember')
        ->and($rules['remember'])->toContain('boolean');
});

it('creates correct throttle key with company identifier', function () {
    $throttleKey = $this->request->throttleKey();

    expect($throttleKey)->toContain('company@example.com')
        ->and($throttleKey)->toContain('127.0.0.1')
        ->and($throttleKey)->toContain('company');
});

it('authenticates valid company credentials', function () {
    // Create a company with known credentials
    Company::factory()->create([
        'email' => 'company@example.com',
        'password' => bcrypt('password'),
    ]);

    // Mock Auth facade
    Auth::shouldReceive('guard')
        ->with('company')
        ->once()
        ->andReturnSelf();

    Auth::shouldReceive('attempt')
        ->with([
            'email' => 'company@example.com',
            'password' => 'password',
        ], true)
        ->once()
        ->andReturnTrue();

    // Mock RateLimiter
    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturnFalse();
    RateLimiter::shouldReceive('clear')->once();

    // Execute
    $this->request->authenticate();

    // No exception means success
    expect(true)->toBeTrue();
});

it('throws ValidationException for invalid company credentials', function () {
    // Mock Auth facade
    Auth::shouldReceive('guard')
        ->with('company')
        ->once()
        ->andReturnSelf();

    Auth::shouldReceive('attempt')
        ->with([
            'email' => 'company@example.com',
            'password' => 'password',
        ], true)
        ->once()
        ->andReturnFalse();

    // Mock RateLimiter
    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturnFalse();
    RateLimiter::shouldReceive('hit')->once();

    // Execute and expect exception
    expect(fn () => $this->request->authenticate())
        ->toThrow(ValidationException::class);
});

it('throws ValidationException when rate limited for company login', function () {
    Event::fake();

    // Mock RateLimiter
    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturnTrue();
    RateLimiter::shouldReceive('availableIn')->once()->andReturn(60);

    // Execute and expect exception
    expect(fn () => $this->request->ensureIsNotRateLimited())
        ->toThrow(ValidationException::class);

    // Check that Lockout event was fired
    Event::assertDispatched(Lockout::class);
});

it('passes when not rate limited for company login', function () {
    // Mock RateLimiter
    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturnFalse();

    // Execute
    $this->request->ensureIsNotRateLimited();

    // No exception means success
    expect(true)->toBeTrue();
});
