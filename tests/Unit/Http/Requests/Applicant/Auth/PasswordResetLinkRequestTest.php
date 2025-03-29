<?php

declare(strict_types=1);

use App\Http\Requests\Applicant\Auth\PasswordResetLinkRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

test('it authorizes all users to request a password reset', function () {
    $request = new PasswordResetLinkRequest();
    
    expect($request->authorize())->toBeTrue();
});

test('it validates required email field', function () {
    $validator = Validator::make(
        ['email' => ''],
        (new PasswordResetLinkRequest())->rules()
    );
    
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
    expect($validator->errors()->first('email'))->toContain('required');
});

test('it validates email format', function () {
    $validator = Validator::make(
        ['email' => 'not-an-email'],
        (new PasswordResetLinkRequest())->rules()
    );
    
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
    expect($validator->errors()->first('email'))->toContain('valid email');
});

test('it converts email to lowercase', function () {
    $rules = (new PasswordResetLinkRequest())->rules();
    
    expect($rules['email'])->toContain('lowercase');
});
