<?php

declare(strict_types=1);

use App\Enums\JobCategory;

it('can get enum value', function () {
    expect(JobCategory::SoftwareEngineering->value)->toBe('software_engineering');
    expect(JobCategory::DevOps->value)->toBe('devops');
});

it('can get label', function () {
    expect(JobCategory::SoftwareEngineering->label())->toBe('Software Engineering/Development');
    expect(JobCategory::DevOps->label())->toBe('DevOps & Site Reliability Engineering');
    expect(JobCategory::GameDevelopment->label())->toBe('Game Development');
});

it('can get all values', function () {
    $values = JobCategory::values();

    expect($values)->toBeArray();
    expect($values)->toContain('software_engineering');
    expect($values)->toContain('devops');
    expect($values)->toHaveCount(20);
});

it('can get all options', function () {
    $options = JobCategory::options();

    expect($options)->toBeArray();
    expect($options)->toHaveKey('software_engineering');
    expect($options)->toHaveKey('devops');
    expect($options['software_engineering'])->toBe('Software Engineering/Development');
});
