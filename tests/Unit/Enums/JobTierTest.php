<?php

declare(strict_types=1);

use App\Enums\JobTier;

it('has valid values', function () {
    expect(JobTier::values())->toBe([
        'basic',
        'premium',
        'enterprise',
    ]);
});

it('returns correct labels', function () {
    expect(JobTier::BASIC->label())->toBe('Basic');
    expect(JobTier::PREMIUM->label())->toBe('Premium');
    expect(JobTier::ENTERPRISE->label())->toBe('Enterprise');
});

it('can be cast to string', function () {
    expect((string) JobTier::BASIC->value)->toBe('basic');
    expect((string) JobTier::PREMIUM->value)->toBe('premium');
    expect((string) JobTier::ENTERPRISE->value)->toBe('enterprise');
});
