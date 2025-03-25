<?php

declare(strict_types=1);

use App\Enums\Workplace;

it('has valid values', function () {
    expect(Workplace::values())->toBe([
        'remote',
        'onsite',
        'hybrid',
    ]);
});

it('returns correct labels', function () {
    expect(Workplace::REMOTE->label())->toBe('Remote');
    expect(Workplace::ONSITE->label())->toBe('Onsite');
    expect(Workplace::HYBRID->label())->toBe('Hybrid');
});

it('can be cast to string', function () {
    expect((string) Workplace::REMOTE->value)->toBe('remote');
    expect((string) Workplace::ONSITE->value)->toBe('onsite');
    expect((string) Workplace::HYBRID->value)->toBe('hybrid');
});
