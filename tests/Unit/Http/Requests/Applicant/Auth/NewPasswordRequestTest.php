<?php

declare(strict_types=1);

use App\Http\Requests\Applicant\Auth\NewPasswordRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

uses(RefreshDatabase::class);

test('it authorizes all users to reset password', function () {
    $request = new NewPasswordRequest();

    expect($request->authorize())->toBeTrue();
});

test('it validates required fields', function () {
    $validator = Validator::make(
        [
            'token' => '',
            'email' => '',
            'password' => '',
        ],
        (new NewPasswordRequest())->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('token'))->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('it validates email format', function () {
    $validator = Validator::make(
        [
            'token' => 'valid-token',
            'email' => 'not-an-email',
            'password' => 'valid-password',
            'password_confirmation' => 'valid-password',
        ],
        (new NewPasswordRequest())->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('it validates password confirmation', function () {
    $validator = Validator::make(
        [
            'token' => 'valid-token',
            'email' => 'test@example.com',
            'password' => 'valid-password',
            'password_confirmation' => 'different-password',
        ],
        (new NewPasswordRequest())->rules()
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('it applies default password rules', function () {
    $rules = (new NewPasswordRequest())->rules();

    // Check if Rules\Password::defaults() is applied
    $passwordRules = $rules['password'];
    $hasPasswordRule = false;

    foreach ($passwordRules as $rule) {
        if ($rule instanceof Password) {
            $hasPasswordRule = true;
            break;
        }
    }

    expect($hasPasswordRule)->toBeTrue();
});

test('it converts email to lowercase', function () {
    $rules = (new NewPasswordRequest())->rules();

    expect($rules['email'])->toContain('lowercase');
});
