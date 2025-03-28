<?php

declare(strict_types=1);

use App\Enums\ApplicationProcess;
use App\Enums\JobStatus;
use App\Http\Requests\JobListing\UpdateJobListingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

it('passes validation with valid data', function () {
    // Arrange
    $data = [
        'title' => 'Updated PHP Developer Position',
        'description' => 'We are updating our job listing for a PHP developer.',
        'application_process' => ApplicationProcess::EMAIL->value,
        'status' => JobStatus::PUBLISHED->value,
    ];

    // Act
    $request = new UpdateJobListingRequest();
    $validator = Validator::make($data, $request->rules());

    // Assert
    expect($validator->fails())->toBeFalse()
        ->and($validator->errors()->all())->toBeEmpty();
});

it('fails validation when required fields are missing', function () {
    // Arrange
    $data = [
        // Missing required fields
    ];

    // Act
    $request = new UpdateJobListingRequest();
    $validator = Validator::make($data, $request->rules());

    // Assert
    expect($validator->fails())->toBeTrue();

    $errors = $validator->errors();
    expect($errors->has('title'))->toBeTrue()
        ->and($errors->has('description'))->toBeTrue()
        ->and($errors->has('application_process'))->toBeTrue()
        ->and($errors->has('status'))->toBeTrue();
});

it('validates salary range correctly', function () {
    // Arrange - Valid salary range
    $validData = [
        'title' => 'PHP Developer',
        'description' => 'PHP Developer job',
        'application_process' => ApplicationProcess::EMAIL->value,
        'status' => JobStatus::DRAFT->value,
        'salary_min' => 50000,
        'salary_max' => 80000,
    ];

    // Arrange - Invalid salary range (min > max)
    $invalidData = [
        'title' => 'PHP Developer',
        'description' => 'PHP Developer job',
        'application_process' => ApplicationProcess::EMAIL->value,
        'status' => JobStatus::DRAFT->value,
        'salary_min' => 90000,
        'salary_max' => 80000, // Less than min
    ];

    // Act & Assert for valid data
    $request = new UpdateJobListingRequest();
    $validator = Validator::make($validData, $request->rules());
    expect($validator->fails())->toBeFalse();

    // Act & Assert for invalid data
    $validator = Validator::make($invalidData, $request->rules());
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('salary_max'))->toBeTrue();
});
