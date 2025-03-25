<?php

declare(strict_types=1);

use App\Enums\EmploymentType;

it('has valid values', function () {
    expect(EmploymentType::values())->toBe([
        'full-time',
        'part-time',
        'full-part-time',
        'contract',
        'temporary',
        'internship',
        'volunteer',
    ]);
});

it('returns correct labels', function () {
    expect(EmploymentType::FULL_TIME->label())->toBe('Full time');
    expect(EmploymentType::PART_TIME->label())->toBe('Part time');
    expect(EmploymentType::FULL_PART_TIME->label())->toBe('Full/Part time');
    expect(EmploymentType::CONTRACT->label())->toBe('Contract');
    expect(EmploymentType::TEMPORARY->label())->toBe('Temporary');
    expect(EmploymentType::INTERNSHIP->label())->toBe('Internship');
    expect(EmploymentType::VOLUNTEER->label())->toBe('Volunteer');
});

it('can be cast to string', function () {
    expect((string) EmploymentType::FULL_TIME->value)->toBe('full-time');
    expect((string) EmploymentType::PART_TIME->value)->toBe('part-time');
    expect((string) EmploymentType::FULL_PART_TIME->value)->toBe('full-part-time');
    expect((string) EmploymentType::CONTRACT->value)->toBe('contract');
    expect((string) EmploymentType::TEMPORARY->value)->toBe('temporary');
    expect((string) EmploymentType::INTERNSHIP->value)->toBe('internship');
    expect((string) EmploymentType::VOLUNTEER->value)->toBe('volunteer');
});
