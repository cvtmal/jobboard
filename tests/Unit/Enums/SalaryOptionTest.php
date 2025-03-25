<?php

declare(strict_types=1);

use App\Enums\SalaryOption;

it('has valid values', function () {
    expect(SalaryOption::values())->toBe([
        'fixed',
        'range',
        'negotiable',
    ]);
});

it('returns correct labels', function () {
    expect(SalaryOption::FIXED->label())->toBe('Fixed');
    expect(SalaryOption::RANGE->label())->toBe('Range');
    expect(SalaryOption::NEGOTIABLE->label())->toBe('Negotiable');
});

it('can be cast to string', function () {
    expect((string) SalaryOption::FIXED->value)->toBe('fixed');
    expect((string) SalaryOption::RANGE->value)->toBe('range');
    expect((string) SalaryOption::NEGOTIABLE->value)->toBe('negotiable');
});
