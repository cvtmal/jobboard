<?php

declare(strict_types=1);

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

test('admin is authenticatable', function (): void {
    $admin = new Admin();

    expect($admin)->toBeInstanceOf(Authenticatable::class);
});

test('admin factory creates valid instance', function (): void {
    $admin = Admin::factory()->create();

    expect($admin)
        ->toBeInstanceOf(Admin::class)
        ->name->not->toBeEmpty()
        ->email->toContain('@')
        ->password->not->toBeEmpty()
        // Admin still has email_verified_at field but doesn't implement MustVerifyEmail
        ->email_verified_at->not->toBeNull();
});

test('admin factory unverified state works', function (): void {
    // We still test the unverified state even though admins don't need to verify email
    $admin = Admin::factory()->unverified()->create();

    // For admins, we use an old date ('2000-01-01 00:00:00') instead of null
    // since the column is NOT NULL in the database
    expect($admin->email_verified_at->year)->toBe(2000);
    expect($admin->email_verified_at->month)->toBe(1);
    expect($admin->email_verified_at->day)->toBe(1);
});

test('admin password is hashed', function (): void {
    $password = 'password123';
    $admin = Admin::factory()->create([
        'password' => Hash::make($password),
    ]);

    expect(Hash::check($password, $admin->password))->toBeTrue();
});
