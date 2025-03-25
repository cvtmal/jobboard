<?php

declare(strict_types=1);

use App\Enums\ExperienceLevel;

it('has valid values', function () {
    expect(ExperienceLevel::values())->toBe([
        'entry',
        'junior',
        'mid-level',
        'senior',
        'executive',
    ]);
});

it('returns correct labels', function () {
    expect(ExperienceLevel::ENTRY->label())->toBe('Entry Level');
    expect(ExperienceLevel::JUNIOR->label())->toBe('Junior');
    expect(ExperienceLevel::MID_LEVEL->label())->toBe('Mid-Level');
    expect(ExperienceLevel::SENIOR->label())->toBe('Senior');
    expect(ExperienceLevel::EXECUTIVE->label())->toBe('Executive');
});

it('can be cast to string', function () {
    expect((string) ExperienceLevel::ENTRY->value)->toBe('entry');
    expect((string) ExperienceLevel::JUNIOR->value)->toBe('junior');
    expect((string) ExperienceLevel::MID_LEVEL->value)->toBe('mid-level');
    expect((string) ExperienceLevel::SENIOR->value)->toBe('senior');
    expect((string) ExperienceLevel::EXECUTIVE->value)->toBe('executive');
});
