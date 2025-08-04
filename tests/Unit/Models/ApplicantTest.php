<?php

declare(strict_types=1);

use App\Enums\EmploymentType;
use App\Enums\Workplace;
use App\Models\Applicant;
use App\Models\JobApplication;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('applicant is authenticatable', function (): void {
    $applicant = new Applicant();

    expect($applicant)->toBeInstanceOf(Authenticatable::class);
});

test('applicant factory creates valid instance', function (): void {
    $applicant = Applicant::factory()->create();

    expect($applicant)
        ->toBeInstanceOf(Applicant::class)
        ->first_name->not->toBeEmpty()
        ->last_name->not->toBeEmpty()
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

test('applicant has full name accessor', function (): void {
    $applicant = Applicant::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    expect($applicant->full_name)->toBe('John Doe');
});

test('applicant has job applications relationship', function (): void {
    $applicant = new Applicant();

    expect($applicant->jobApplications())->toBeInstanceOf(HasMany::class);
});

test('applicant can have job applications', function (): void {
    $applicant = Applicant::factory()->create();

    // Create job applications for the applicant
    JobApplication::factory()->count(3)->create([
        'applicant_id' => $applicant->id,
    ]);

    expect($applicant->jobApplications)->toHaveCount(3)
        ->each->toBeInstanceOf(JobApplication::class);
});

test('applicant workplace preference is cast to enum', function (): void {
    $applicant = Applicant::factory()->create([
        'workplace_preference' => Workplace::REMOTE->value,
    ]);

    expect($applicant->workplace_preference)
        ->toBeInstanceOf(Workplace::class)
        ->toBe(Workplace::REMOTE);

    $applicant = Applicant::factory()->preferRemote()->create();

    expect($applicant->workplace_preference)->toBe(Workplace::REMOTE);
});

test('applicant employment type preference is cast to enum', function (): void {
    $applicant = Applicant::factory()->create([
        'employment_type_preference' => EmploymentType::PERMANENT->value,
    ]);

    expect($applicant->employment_type_preference)
        ->toBeInstanceOf(EmploymentType::class)
        ->toBe(EmploymentType::PERMANENT);

    $applicant = Applicant::factory()->preferEmploymentType(EmploymentType::TEMPORARY)->create();

    expect($applicant->employment_type_preference)->toBe(EmploymentType::TEMPORARY);
});

test('dates are cast correctly', function (): void {
    $today = now()->startOfDay();

    $applicant = Applicant::factory()->create([
        'available_from' => $today,
        'date_of_birth' => $today->copy()->subYears(30),
    ]);

    expect($applicant->available_from->startOfDay())->toEqual($today);
    expect($applicant->date_of_birth)->toBeInstanceOf(Carbon\CarbonImmutable::class);
});

test('work permit is cast to boolean', function (): void {
    $applicant = Applicant::factory()->withWorkPermit()->create();
    expect($applicant->work_permit)->toBeTrue();

    $applicant = Applicant::factory()->withoutWorkPermit()->create();
    expect($applicant->work_permit)->toBeFalse();
});

test('salary expectation is cast to decimal', function (): void {
    $salary = 75000.50;

    $applicant = Applicant::factory()->create([
        'salary_expectation' => $salary,
    ]);

    expect((float) $applicant->salary_expectation)->toBe($salary);
});

test('applicant factory available immediately state works', function (): void {
    $today = now()->startOfDay();

    $applicant = Applicant::factory()->availableImmediately()->create();

    expect($applicant->available_from->startOfDay())->toEqual($today);
});

test('all applicant fields can be persisted and retrieved', function (): void {
    // Create applicant with all fields filled
    $applicant = Applicant::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane.smith@example.com',
        'phone' => '123-456-7890',
        'mobile_phone' => '098-765-4321',
        'headline' => 'Senior Developer',
        'bio' => 'Experienced developer with 10 years of experience',
        'work_permit' => true,
        'employment_type_preference' => EmploymentType::PERMANENT,
        'workplace_preference' => Workplace::REMOTE,
        'available_from' => now()->addWeek(),
        'salary_expectation' => 85000,
        'resume_path' => 'uploads/resumes/jane-smith-resume.pdf',
        'profile_photo_path' => 'uploads/profile_photos/jane-smith.jpg',
        'portfolio_url' => 'https://portfolio.janesmith.com',
        'linkedin_url' => 'https://linkedin.com/in/janesmith',
        'github_url' => 'https://github.com/janesmith',
        'website_url' => 'https://janesmith.com',
        'date_of_birth' => now()->subYears(35),
        'address' => '123 Main St',
        'city' => 'New York',
        'state' => 'NY',
        'postal_code' => '10001',
        'country' => 'USA',
    ]);

    // Refresh from database
    $retrievedApplicant = Applicant::find($applicant->id);

    // Test all fields are retrieved correctly
    expect($retrievedApplicant)
        ->first_name->toBe('Jane')
        ->last_name->toBe('Smith')
        ->email->toBe('jane.smith@example.com')
        ->phone->toBe('123-456-7890')
        ->mobile_phone->toBe('098-765-4321')
        ->headline->toBe('Senior Developer')
        ->bio->toBe('Experienced developer with 10 years of experience')
        ->work_permit->toBeTrue()
        ->employment_type_preference->toBe(EmploymentType::PERMANENT)
        ->workplace_preference->toBe(Workplace::REMOTE)
        ->resume_path->toBe('uploads/resumes/jane-smith-resume.pdf')
        ->profile_photo_path->toBe('uploads/profile_photos/jane-smith.jpg')
        ->portfolio_url->toBe('https://portfolio.janesmith.com')
        ->linkedin_url->toBe('https://linkedin.com/in/janesmith')
        ->github_url->toBe('https://github.com/janesmith')
        ->website_url->toBe('https://janesmith.com')
        ->address->toBe('123 Main St')
        ->city->toBe('New York')
        ->state->toBe('NY')
        ->postal_code->toBe('10001')
        ->country->toBe('USA');

    // Test date fields
    expect($retrievedApplicant->available_from->format('Y-m-d'))
        ->toBe(now()->addWeek()->format('Y-m-d'));
    expect($retrievedApplicant->date_of_birth->format('Y-m-d'))
        ->toBe(now()->subYears(35)->format('Y-m-d'));
});
