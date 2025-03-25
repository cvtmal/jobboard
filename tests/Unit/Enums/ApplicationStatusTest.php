<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;

it('has valid values', function () {
    expect(ApplicationStatus::values())->toBe([
        'new',
        'pending',
        'reviewing',
        'shortlisted',
        'interviewing',
        'offered',
        'hired',
        'rejected',
    ]);
});

it('returns correct labels', function () {
    expect(ApplicationStatus::NEW->label())->toBe('New');
    expect(ApplicationStatus::PENDING->label())->toBe('Pending');
    expect(ApplicationStatus::REVIEWING->label())->toBe('Reviewing');
    expect(ApplicationStatus::SHORTLISTED->label())->toBe('Shortlisted');
    expect(ApplicationStatus::INTERVIEWING->label())->toBe('Interviewing');
    expect(ApplicationStatus::OFFERED->label())->toBe('Offered');
    expect(ApplicationStatus::HIRED->label())->toBe('Hired');
    expect(ApplicationStatus::REJECTED->label())->toBe('Rejected');
});

it('can be cast to string', function () {
    expect((string) ApplicationStatus::NEW->value)->toBe('new');
    expect((string) ApplicationStatus::PENDING->value)->toBe('pending');
    expect((string) ApplicationStatus::REVIEWING->value)->toBe('reviewing');
    expect((string) ApplicationStatus::SHORTLISTED->value)->toBe('shortlisted');
    expect((string) ApplicationStatus::INTERVIEWING->value)->toBe('interviewing');
    expect((string) ApplicationStatus::OFFERED->value)->toBe('offered');
    expect((string) ApplicationStatus::HIRED->value)->toBe('hired');
    expect((string) ApplicationStatus::REJECTED->value)->toBe('rejected');
});
