<?php

declare(strict_types=1);

use App\Actions\Company\CreateCompanyAction;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a company with required fields', function () {
    $action = new CreateCompanyAction();

    $data = [
        'name' => 'Test Company',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $company = $action->execute($data);

    expect($company)->toBeInstanceOf(Company::class)
        ->and($company->name)->toBe('Test Company')
        ->and($company->email)->toBe('test@example.com')
        ->and($company->active)->toBeTrue()
        ->and($company->blocked)->toBeFalse()
        ->and(password_verify('password123', $company->password))->toBeTrue();
});

it('creates a company with optional fields', function () {
    $action = new CreateCompanyAction();

    $data = [
        'name' => 'Test Company',
        'email' => 'test@example.com',
        'password' => 'password123',
        'address' => '123 Test Street',
        'postcode' => '12345',
        'city' => 'Test City',
        'url' => 'https://example.com',
    ];

    $company = $action->execute($data);

    expect($company)->toBeInstanceOf(Company::class)
        ->and($company->name)->toBe('Test Company')
        ->and($company->email)->toBe('test@example.com')
        ->and($company->address)->toBe('123 Test Street')
        ->and($company->postcode)->toBe('12345')
        ->and($company->city)->toBe('Test City')
        ->and($company->url)->toBe('https://example.com')
        ->and($company->active)->toBeTrue()
        ->and($company->blocked)->toBeFalse();
});
