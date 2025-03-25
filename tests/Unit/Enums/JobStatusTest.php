<?php

declare(strict_types=1);

use App\Enums\JobStatus;

it('has valid values', function () {
    expect(JobStatus::values())->toBe([
        'draft',
        'pending',
        'published',
        'expired',
        'closed',
    ]);
});

it('returns correct labels', function () {
    expect(JobStatus::DRAFT->label())->toBe('Draft');
    expect(JobStatus::PENDING->label())->toBe('Pending');
    expect(JobStatus::PUBLISHED->label())->toBe('Published');
    expect(JobStatus::EXPIRED->label())->toBe('Expired');
    expect(JobStatus::CLOSED->label())->toBe('Closed');
});

it('can be cast to string', function () {
    expect((string) JobStatus::DRAFT->value)->toBe('draft');
    expect((string) JobStatus::PENDING->value)->toBe('pending');
    expect((string) JobStatus::PUBLISHED->value)->toBe('published');
    expect((string) JobStatus::EXPIRED->value)->toBe('expired');
    expect((string) JobStatus::CLOSED->value)->toBe('closed');
});
