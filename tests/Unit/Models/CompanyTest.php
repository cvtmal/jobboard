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

test('company factory generates all fields correctly', function (): void {
    $company = Company::factory()->create();

    // Test required fields
    expect($company->name)->toBeString()->not->toBeEmpty();
    expect($company->email)->toBeString()->toContain('@');
    expect($company->description_english)->toBeString();
    expect($company->active)->toBeBool();
    expect($company->blocked)->toBeBool();

    // Test nullable fields if they are present
    if ($company->address !== null) {
        expect($company->address)->toBeString();
    }

    if ($company->postcode !== null) {
        expect($company->postcode)->toBeString();
    }

    if ($company->city !== null) {
        expect($company->city)->toBeString();
    }

    if ($company->latitude !== null) {
        expect($company->latitude)->toBeFloat();
    }

    if ($company->longitude !== null) {
        expect($company->longitude)->toBeFloat();
    }

    if ($company->url !== null) {
        expect($company->url)->toBeString();
    }

    if ($company->size !== null) {
        expect($company->size)->toBeString();
    }

    if ($company->type !== null) {
        expect($company->type)->toBeString();
    }

    if ($company->description_german !== null) {
        expect($company->description_german)->toBeString();
    }

    if ($company->description_french !== null) {
        expect($company->description_french)->toBeString();
    }

    if ($company->description_italian !== null) {
        expect($company->description_italian)->toBeString();
    }

    if ($company->logo !== null) {
        expect($company->logo)->toBeString();
    }

    if ($company->cover !== null) {
        expect($company->cover)->toBeString();
    }

    if ($company->video !== null) {
        expect($company->video)->toBeString();
    }

    if ($company->newsletter !== null) {
        expect($company->newsletter)->toBeBool();
    }

    if ($company->internal_notes !== null) {
        expect($company->internal_notes)->toBeString();
    }
});

test('company boolean fields are properly cast', function (): void {
    $company = Company::factory()->create([
        'newsletter' => true,
        'active' => true,
        'blocked' => false,
    ]);

    expect($company->newsletter)->toBeBool()->toBeTrue()
        ->and($company->active)->toBeBool()->toBeTrue()
        ->and($company->blocked)->toBeBool()->toBeFalse();
});

test('company coordinates are properly cast', function (): void {
    $company = Company::factory()->create([
        'latitude' => '12.34567890',
        'longitude' => '98.76543210',
    ]);

    expect($company->latitude)->toBeFloat()->toEqual(12.34567890)
        ->and($company->longitude)->toBeFloat()->toEqual(98.76543210);
});
