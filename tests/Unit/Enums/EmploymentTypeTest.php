<?php

declare(strict_types=1);

use App\Enums\EmploymentType;

it('has valid values', function () {
    expect(EmploymentType::values())->toBe([
        'permanent',
        'temporary',
        'freelance',
        'internship',
        'side-job',
        'apprenticeship',
        'working-student',
        'interim',
    ]);
});

it('returns correct labels', function () {
    expect(EmploymentType::PERMANENT->label())->toBe('Permanent position');
    expect(EmploymentType::TEMPORARY->label())->toBe('Temporary employment');
    expect(EmploymentType::FREELANCE->label())->toBe('Freelance');
    expect(EmploymentType::INTERNSHIP->label())->toBe('Internship');
    expect(EmploymentType::SIDE_JOB->label())->toBe('Side job');
    expect(EmploymentType::APPRENTICESHIP->label())->toBe('Apprenticeship');
    expect(EmploymentType::WORKING_STUDENT->label())->toBe('Working student');
    expect(EmploymentType::INTERIM->label())->toBe('Interim');
});

it('can be cast to string', function () {
    expect((string) EmploymentType::PERMANENT->value)->toBe('permanent');
    expect((string) EmploymentType::TEMPORARY->value)->toBe('temporary');
    expect((string) EmploymentType::FREELANCE->value)->toBe('freelance');
    expect((string) EmploymentType::INTERNSHIP->value)->toBe('internship');
    expect((string) EmploymentType::SIDE_JOB->value)->toBe('side-job');
    expect((string) EmploymentType::APPRENTICESHIP->value)->toBe('apprenticeship');
    expect((string) EmploymentType::WORKING_STUDENT->value)->toBe('working-student');
    expect((string) EmploymentType::INTERIM->value)->toBe('interim');
});
