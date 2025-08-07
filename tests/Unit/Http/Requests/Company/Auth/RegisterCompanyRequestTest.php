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

        ->and($rules)->toHaveKey('first_name')
        ->and($rules['first_name'])->toContain('required')
        ->and($rules['first_name'])->toContain('string')
        ->and($rules['first_name'])->toContain('max:255')

        ->and($rules)->toHaveKey('last_name')
        ->and($rules['last_name'])->toContain('required')
        ->and($rules['last_name'])->toContain('string')
        ->and($rules['last_name'])->toContain('max:255')

        ->and($rules)->toHaveKey('phone_number')
        ->and($rules['phone_number'])->toContain('nullable')
        ->and($rules['phone_number'])->toContain('string')
        ->and($rules['phone_number'])->toContain('max:50')

        ->and($rules)->toHaveKey('email')
        ->and($rules['email'])->toContain('required')
        ->and($rules['email'])->toContain('email')
        ->and($rules['email'])->toContain('lowercase')
        ->and($rules['email'])->toContain('max:255')
        ->and($rules['email'])->toContain('unique:companies')

        ->and($rules)->toHaveKey('password')
        ->and($rules['password'])->toContain('required')
        ->and($rules['password'])->toContain('confirmed');

    // No optional fields remain in validation
    expect($rules)->not->toHaveKey('address')
        ->and($rules)->not->toHaveKey('postcode')
        ->and($rules)->not->toHaveKey('city')
        ->and($rules)->not->toHaveKey('url');

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
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '+1234567890',
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
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '+1234567890',
        'email' => 'new-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ], $this->request->rules());

    // Validation should fail due to mismatched passwords
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});


it('passes validation with valid company data', function () {
    // Set up validator with valid test data
    $validator = validator([
        'name' => 'Test Company',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone_number' => '+1234567890',
        'email' => 'new-company@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $this->request->rules());

    // Validation should pass
    expect($validator->fails())->toBeFalse();
});

it('passes validation with minimal company data', function () {
    // Set up validator with minimal required data (phone number is nullable)
    $validator = validator([
        'name' => 'Test Company',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'minimal@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $this->request->rules());

    // Validation should pass
    expect($validator->fails())->toBeFalse();
});
