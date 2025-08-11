<?php

declare(strict_types=1);

use App\Actions\Applicant\CreateApplicantAction;
use App\Models\Applicant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->action = new CreateApplicantAction();
});

it('creates an applicant with required fields only', function () {
    Event::fake();

    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
    ];

    $applicant = $this->action->execute($data);

    expect($applicant)->toBeInstanceOf(Applicant::class);
    expect($applicant->first_name)->toBe('John');
    expect($applicant->last_name)->toBe('Doe');
    expect($applicant->email)->toBe('john.doe@example.com');
    expect(Hash::check('password123', $applicant->password))->toBeTrue();

    $this->assertDatabaseHas('applicants', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    Event::assertDispatched(Registered::class, function ($event) use ($applicant) {
        return $event->user->is($applicant);
    });
});

it('creates an applicant with all optional fields', function () {
    Event::fake();

    $data = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@example.com',
        'password' => 'password456',
        'address' => '123 Main St',
        'phone' => '+41 79 123 45 67',
        'profile_photo_path' => 'photos/profile.jpg',
        'city' => 'Zurich',
        'country' => 'Switzerland',
        'bio' => 'Experienced software developer',
    ];

    $applicant = $this->action->execute($data);

    expect($applicant)->toBeInstanceOf(Applicant::class);
    expect($applicant->first_name)->toBe('Jane');
    expect($applicant->last_name)->toBe('Smith');
    expect($applicant->email)->toBe('jane.smith@example.com');
    expect($applicant->address)->toBe('123 Main St');
    expect($applicant->phone)->toBe('+41 79 123 45 67');
    expect($applicant->profile_photo_path)->toBe('photos/profile.jpg');
    expect($applicant->city)->toBe('Zurich');
    expect($applicant->country)->toBe('Switzerland');
    expect($applicant->bio)->toBe('Experienced software developer');

    $this->assertDatabaseHas('applicants', [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@example.com',
        'address' => '123 Main St',
        'phone' => '+41 79 123 45 67',
        'city' => 'Zurich',
        'country' => 'Switzerland',
        'bio' => 'Experienced software developer',
    ]);
});

it('hashes the password correctly', function () {
    Event::fake();

    $data = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'plaintext-password',
    ];

    $applicant = $this->action->execute($data);

    expect($applicant->password)->not->toBe('plaintext-password');
    expect(Hash::check('plaintext-password', $applicant->password))->toBeTrue();
});

it('ignores unknown fields in data array', function () {
    Event::fake();

    $data = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'unknown_field' => 'should be ignored',
        'another_unknown' => 123,
    ];

    $applicant = $this->action->execute($data);

    expect($applicant)->toBeInstanceOf(Applicant::class);
    expect($applicant->first_name)->toBe('Test');
    expect($applicant->last_name)->toBe('User');
    expect($applicant->email)->toBe('test@example.com');

    // Verify that unknown fields are not saved
    expect($applicant->getAttributes())->not->toHaveKey('unknown_field');
    expect($applicant->getAttributes())->not->toHaveKey('another_unknown');
});

it('only includes optional fields when they exist in data', function () {
    Event::fake();

    $data = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'city' => 'Basel',
        // Missing other optional fields
    ];

    $applicant = $this->action->execute($data);

    expect($applicant->city)->toBe('Basel');
    expect($applicant->address)->toBeNull();
    expect($applicant->phone)->toBeNull();
    expect($applicant->profile_photo_path)->toBeNull();
    expect($applicant->postcode)->toBeNull();
    expect($applicant->country)->toBeNull();
    expect($applicant->bio)->toBeNull();
});

it('runs in database transaction', function () {
    Event::fake();

    // Mock the Applicant model to throw an exception
    $this->mock(Applicant::class, function ($mock) {
        $mock->shouldReceive('create')
            ->once()
            ->andThrow(new Exception('Database error'));
    });

    $data = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    expect(fn () => $this->action->execute($data))
        ->toThrow(Exception::class, 'Database error');

    // Verify no applicant was created due to transaction rollback
    $this->assertDatabaseMissing('applicants', [
        'email' => 'test@example.com',
    ]);
});
