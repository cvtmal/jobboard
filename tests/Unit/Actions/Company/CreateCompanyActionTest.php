<?php

declare(strict_types=1);

use App\Actions\Company\CreateCompanyAction;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new CreateCompanyAction();
});

it('creates a company with all required fields', function () {
    Event::fake();

    $data = [
        'name' => 'Tech Solutions AG',
        'first_name' => 'John',
        'last_name' => 'Smith',
        'phone_number' => '+41 44 123 45 67',
        'email' => 'contact@techsolutions.ch',
        'password' => 'secure-password',
    ];

    $company = $this->action->execute($data);

    expect($company)->toBeInstanceOf(Company::class);
    expect($company->name)->toBe('Tech Solutions AG');
    expect($company->first_name)->toBe('John');
    expect($company->last_name)->toBe('Smith');
    expect($company->phone_number)->toBe('+41 44 123 45 67');
    expect($company->email)->toBe('contact@techsolutions.ch');
    expect($company->active)->toBeTrue();
    expect($company->blocked)->toBeFalse();
    expect(Hash::check('secure-password', $company->password))->toBeTrue();

    $this->assertDatabaseHas('companies', [
        'name' => 'Tech Solutions AG',
        'first_name' => 'John',
        'last_name' => 'Smith',
        'phone_number' => '+41 44 123 45 67',
        'email' => 'contact@techsolutions.ch',
        'active' => true,
        'blocked' => false,
    ]);
});

it('hashes the password correctly', function () {
    Event::fake();

    $data = [
        'name' => 'Test Company',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'phone_number' => '+41 79 987 65 43',
        'email' => 'test@company.com',
        'password' => 'plaintext-password',
    ];

    $company = $this->action->execute($data);

    expect($company->password)->not->toBe('plaintext-password');
    expect(Hash::check('plaintext-password', $company->password))->toBeTrue();
});

it('sets company as active by default', function () {
    Event::fake();

    $data = [
        'name' => 'Active Company',
        'first_name' => 'Test',
        'last_name' => 'User',
        'phone_number' => '+41 44 555 12 34',
        'email' => 'active@company.com',
        'password' => 'password123',
    ];

    $company = $this->action->execute($data);

    expect($company->active)->toBeTrue();
    expect($company->blocked)->toBeFalse();
});

it('dispatches registered event', function () {
    Event::fake();

    $data = [
        'name' => 'Event Test Company',
        'first_name' => 'Event',
        'last_name' => 'Test',
        'phone_number' => '+41 31 888 99 00',
        'email' => 'event@test.com',
        'password' => 'password456',
    ];

    $company = $this->action->execute($data);

    Event::assertDispatched(Registered::class, function ($event) use ($company) {
        return $event->user->is($company);
    });
});

it('creates company with unicode characters', function () {
    Event::fake();

    $data = [
        'name' => 'Zürich Öl & Gas GmbH',
        'first_name' => 'François',
        'last_name' => 'Müller',
        'phone_number' => '+41 44 123 45 67',
        'email' => 'contact@zürich-oil.ch',
        'password' => 'password123',
    ];

    $company = $this->action->execute($data);

    expect($company->name)->toBe('Zürich Öl & Gas GmbH');
    expect($company->first_name)->toBe('François');
    expect($company->last_name)->toBe('Müller');
    expect($company->email)->toBe('contact@zürich-oil.ch');
});

it('ignores extra fields in data array', function () {
    Event::fake();

    $data = [
        'name' => 'Extra Fields Company',
        'first_name' => 'Extra',
        'last_name' => 'Fields',
        'phone_number' => '+41 44 111 22 33',
        'email' => 'extra@fields.com',
        'password' => 'password123',
        'website' => 'https://example.com',
        'description' => 'This should be ignored',
        'extra_field' => 'ignored',
    ];

    $company = $this->action->execute($data);

    expect($company)->toBeInstanceOf(Company::class);
    expect($company->name)->toBe('Extra Fields Company');

    // Verify extra fields are not saved
    expect($company->getAttributes())->not->toHaveKey('website');
    expect($company->getAttributes())->not->toHaveKey('description');
    expect($company->getAttributes())->not->toHaveKey('extra_field');
});
