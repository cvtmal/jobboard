<?php

declare(strict_types=1);

use App\Enums\SalaryType;

it('has valid values', function () {
    expect(SalaryType::values())->toBe([
        'hourly',
        'daily',
        'monthly',
        'yearly',
    ]);
});

it('returns correct labels', function () {
    expect(SalaryType::HOURLY->label())->toBe('Hourly');
    expect(SalaryType::DAILY->label())->toBe('Daily');
    expect(SalaryType::MONTHLY->label())->toBe('Monthly');
    expect(SalaryType::YEARLY->label())->toBe('Yearly');
});

it('can be cast to string', function () {
    expect((string) SalaryType::HOURLY->value)->toBe('hourly');
    expect((string) SalaryType::DAILY->value)->toBe('daily');
    expect((string) SalaryType::MONTHLY->value)->toBe('monthly');
    expect((string) SalaryType::YEARLY->value)->toBe('yearly');
});
