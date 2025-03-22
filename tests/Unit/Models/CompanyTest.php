<?php

declare(strict_types=1);

use App\Models\Company;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

test('company is authenticatable', function (): void {
    $company = new Company();

    expect($company)->toBeInstanceOf(Authenticatable::class);
});

test('company factory creates valid instance', function (): void {
    $company = Company::factory()->create();

    expect($company)
        ->toBeInstanceOf(Company::class)
        ->name->not->toBeEmpty()
        ->email->toContain('@')
        ->password->not->toBeEmpty()
        ->email_verified_at->not->toBeNull();
});

test('company factory unverified state works', function (): void {
    $company = Company::factory()->unverified()->create();

    expect($company->email_verified_at)->toBeNull();
});

test('company password is hashed', function (): void {
    $password = 'password123';
    $company = Company::factory()->create([
        'password' => Hash::make($password),
    ]);

    expect(Hash::check($password, $company->password))->toBeTrue();
});
