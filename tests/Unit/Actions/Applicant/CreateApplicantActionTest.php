<?php

declare(strict_types=1);

use App\Actions\Applicant\CreateApplicantAction;
use App\Models\Applicant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

it('creates an applicant with required fields', function () {
    Event::fake();
    $action = new CreateApplicantAction();

    $data = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
    ];

    $applicant = $action->execute($data);

    expect($applicant)->toBeInstanceOf(Applicant::class)
        ->and($applicant->first_name)->toBe('John')
        ->and($applicant->last_name)->toBe('Doe')
        ->and($applicant->email)->toBe('john.doe@example.com')
        ->and(password_verify('password123', $applicant->password))->toBeTrue();

    Event::assertDispatched(Registered::class, function ($event) use ($applicant) {
        return $event->user->id === $applicant->id;
    });
});

it('creates an applicant with optional fields', function () {
    Event::fake();
    $action = new CreateApplicantAction();

    $data = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@example.com',
        'password' => 'password123',
        'address' => '123 Test Street',
        'phone' => '+1234567890',
        'profile_photo_path' => 'profile/photo.jpg',
    ];

    $applicant = $action->execute($data);

    expect($applicant)->toBeInstanceOf(Applicant::class)
        ->and($applicant->first_name)->toBe('Jane')
        ->and($applicant->last_name)->toBe('Smith')
        ->and($applicant->email)->toBe('jane.smith@example.com')
        ->and($applicant->address)->toBe('123 Test Street')
        ->and($applicant->phone)->toBe('+1234567890')
        ->and($applicant->profile_photo_path)->toBe('profile/photo.jpg')
        ->and(password_verify('password123', $applicant->password))->toBeTrue();

    Event::assertDispatched(Registered::class);
});

it('properly hashes the password when creating an applicant', function () {
    Event::fake();
    $action = new CreateApplicantAction();

    $data = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test.user@example.com',
        'password' => 'securepassword',
    ];

    $applicant = $action->execute($data);

    expect($applicant->password)->not->toBe('securepassword')
        ->and(password_verify('securepassword', $applicant->password))->toBeTrue();

    Event::assertDispatched(Registered::class);
});
