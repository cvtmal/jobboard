<?php

declare(strict_types=1);

use App\Models\Applicant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

test('applicant is authenticatable', function (): void {
    $applicant = new Applicant();

    expect($applicant)->toBeInstanceOf(Authenticatable::class);
});

test('applicant factory creates valid instance', function (): void {
    $applicant = Applicant::factory()->create();

    expect($applicant)
        ->toBeInstanceOf(Applicant::class)
        ->name->not->toBeEmpty()
        ->email->toContain('@')
        ->password->not->toBeEmpty()
        ->email_verified_at->not->toBeNull();
});

test('applicant factory unverified state works', function (): void {
    $applicant = Applicant::factory()->unverified()->create();

    expect($applicant->email_verified_at)->toBeNull();
});

test('applicant password is hashed', function (): void {
    $password = 'password123';
    $applicant = Applicant::factory()->create([
        'password' => Hash::make($password),
    ]);

    expect(Hash::check($password, $applicant->password))->toBeTrue();
});
