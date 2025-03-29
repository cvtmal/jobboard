<?php

declare(strict_types=1);

use App\Http\Requests\Company\Auth\RegisterCompanyRequest;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules\Password;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->request = new RegisterCompanyRequest();
});

it('is always authorized', function () {
    expect($this->request->authorize())->toBeTrue();
});

it('has the expected validation rules', function () {
    $rules = $this->request->rules();

    // Required fields
    expect($rules)->toHaveKey('name')
        ->and($rules['name'])->toContain('required')
        ->and($rules['name'])->toContain('string')
        ->and($rules['name'])->toContain('max:255')

        ->and($rules)->toHaveKey('email')
        ->and($rules['email'])->toContain('required')
        ->and($rules['email'])->toContain('email')
        ->and($rules['email'])->toContain('lowercase')
        ->and($rules['email'])->toContain('max:255')
        ->and($rules['email'])->toContain('unique:companies')

        ->and($rules)->toHaveKey('password')
        ->and($rules['password'])->toContain('required')
        ->and($rules['password'])->toContain('confirmed');

    // Optional fields
    expect($rules)->toHaveKey('address')
        ->and($rules['address'])->toContain('nullable')
        ->and($rules['address'])->toContain('string')
        ->and($rules['address'])->toContain('max:255')

        ->and($rules)->toHaveKey('postcode')
        ->and($rules['postcode'])->toContain('nullable')
        ->and($rules['postcode'])->toContain('string')
        ->and($rules['postcode'])->toContain('max:20')

        ->and($rules)->toHaveKey('city')
        ->and($rules['city'])->toContain('nullable')
        ->and($rules['city'])->toContain('string')
        ->and($rules['city'])->toContain('max:100')

        ->and($rules)->toHaveKey('url')
        ->and($rules['url'])->toContain('nullable')
        ->and($rules['url'])->toContain('url')
        ->and($rules['url'])->toContain('max:255');

    // Password rule is an object, so check it differently
    $passwordRules = collect($rules['password'])->filter(fn ($rule) => $rule instanceof Password);
    expect($passwordRules)->toHaveCount(1);
});

it('enforces unique email validation for companies', function () {
    // Create a company with a known email
    Company::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Set up validator with test data
    $validator = validator([
        'name' => 'Test Company',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $this->request->rules());

    // Validation should fail due to duplicate email
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('validates mismatched passwords for company registration', function () {
    // Set up validator with test data that has mismatched passwords
    $validator = validator([
        'name' => 'Test Company',
        'email' => 'new-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ], $this->request->rules());

    // Validation should fail due to mismatched passwords
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

it('validates optional company fields', function () {
    // Invalid URL
    $validator = validator([
        'name' => 'Test Company',
        'email' => 'valid@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'url' => 'not-a-valid-url',
    ], $this->request->rules());

    // Should fail due to invalid URL
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('url'))->toBeTrue();
});

it('passes validation with valid company data', function () {
    // Set up validator with valid test data
    $validator = validator([
        'name' => 'Test Company',
        'email' => 'new-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'address' => '123 Company Street',
        'postcode' => '12345',
        'city' => 'Business City',
        'url' => 'https://example.com',
    ], $this->request->rules());

    // Validation should pass
    expect($validator->fails())->toBeFalse();
});
