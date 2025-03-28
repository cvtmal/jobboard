<?php

declare(strict_types=1);

use App\Enums\ApplicationProcess;
use App\Enums\JobStatus;
use App\Http\Requests\JobListing\CreateJobListingRequest;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

it('passes validation with valid data', function () {
    // Arrange
    $company = Company::factory()->create();

    $data = [
        'title' => 'PHP Developer Position',
        'description' => 'We are looking for a PHP developer.',
        'company_id' => $company->id,
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::DRAFT,
    ];

    // Act
    $request = new CreateJobListingRequest();
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
    $request = new CreateJobListingRequest();
    $validator = Validator::make($data, $request->rules());

    // Assert
    expect($validator->fails())->toBeTrue();

    $errors = $validator->errors();
    expect($errors->has('title'))->toBeTrue()
        ->and($errors->has('description'))->toBeTrue()
        ->and($errors->has('company_id'))->toBeTrue()
        ->and($errors->has('application_process'))->toBeTrue()
        ->and($errors->has('status'))->toBeTrue();
});

it('validates company_id exists in companies table', function () {
    // Arrange
    $nonExistentCompanyId = 999;
    $data = [
        'title' => 'PHP Developer',
        'description' => 'PHP Developer job',
        'company_id' => $nonExistentCompanyId,
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::DRAFT,
    ];

    // Act
    $request = new CreateJobListingRequest();
    $validator = Validator::make($data, $request->rules());

    // Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('company_id'))->toBeTrue();
});
