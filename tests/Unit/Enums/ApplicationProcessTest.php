<?php

declare(strict_types=1);

use App\Enums\ApplicationProcess;

it('has valid values', function () {
    $values = ApplicationProcess::values();

    expect($values)->toBeArray();
    foreach ($values as $value) {
        expect($value)->toBeString();
    }
});

it('can be cast to string', function () {
    foreach (ApplicationProcess::cases() as $case) {
        expect((string) $case->value)->toBeString();
    }
});
