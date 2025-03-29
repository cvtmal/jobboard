<?php

declare(strict_types=1);

use App\Http\Requests\Applicant\Auth\RegisterApplicantRequest;
use App\Models\Applicant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules\Password;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->request = new RegisterApplicantRequest();
});

it('is always authorized', function () {
    expect($this->request->authorize())->toBeTrue();
});

it('has the expected validation rules', function () {
    $rules = $this->request->rules();
    
    expect($rules)->toHaveKey('first_name')
        ->and($rules['first_name'])->toContain('required')
        ->and($rules['first_name'])->toContain('string')
        ->and($rules['first_name'])->toContain('max:255')
        ->and($rules)->toHaveKey('last_name')
        ->and($rules['last_name'])->toContain('required')
        ->and($rules['last_name'])->toContain('string')
        ->and($rules['last_name'])->toContain('max:255')
        ->and($rules)->toHaveKey('email')
        ->and($rules['email'])->toContain('required')
        ->and($rules['email'])->toContain('email')
        ->and($rules['email'])->toContain('lowercase')
        ->and($rules['email'])->toContain('max:255')
        ->and($rules['email'])->toContain('unique:applicants')
        ->and($rules)->toHaveKey('password')
        ->and($rules['password'])->toContain('required')
        ->and($rules['password'])->toContain('confirmed');
        
    // Password rule is an object, so check it differently
    $passwordRules = collect($rules['password'])->filter(fn ($rule) => $rule instanceof Password);
    expect($passwordRules)->toHaveCount(1);
});

it('enforces unique email validation', function () {
    // Create an applicant with a known email
    Applicant::factory()->create([
        'email' => 'existing@example.com',
    ]);
    
    // Set up validator with test data
    $validator = validator([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $this->request->rules());
    
    // Validation should fail due to duplicate email
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('validates mismatched passwords', function () {
    // Set up validator with test data that has mismatched passwords
    $validator = validator([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ], $this->request->rules());
    
    // Validation should fail due to mismatched passwords
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

it('passes validation with valid data', function () {
    // Set up validator with valid test data
    $validator = validator([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $this->request->rules());
    
    // Validation should pass
    expect($validator->fails())->toBeFalse();
});
