<?php

declare(strict_types=1);

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use App\Http\Requests\JobListing\UpdateJobListingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

it('validates correctly with valid data', function () {
    // Test case 1: Minimal valid data
    $minimalData = [
        'title' => 'Updated Job Title',
        'description' => 'Updated job description content',
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::DRAFT,
    ];

    // Test case 2: With employment type
    $withEmploymentType = [
        'title' => 'Senior Developer',
        'description' => 'Senior developer position',
        'employment_type' => EmploymentType::PERMANENT,
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::PUBLISHED,
    ];

    // Test case 3: With workplace
    $withWorkplace = [
        'title' => 'Remote Developer',
        'description' => 'Work from anywhere',
        'workplace' => Workplace::REMOTE,
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::PUBLISHED,
    ];

    // Test case 4: With salary info
    $withSalaryInfo = [
        'title' => 'Developer with Salary',
        'description' => 'Competitive salary',
        'no_salary' => false,
        'salary_type' => SalaryType::YEARLY,
        'salary_option' => SalaryOption::RANGE,
        'salary_min' => 80000,
        'salary_max' => 120000,
        'salary_currency' => 'CHF',
        'application_process' => ApplicationProcess::EMAIL,
        'status' => JobStatus::PUBLISHED,
    ];

    $testCases = [
        $minimalData,
        $withEmploymentType,
        $withWorkplace,
        $withSalaryInfo,
    ];

    $request = new UpdateJobListingRequest();

    foreach ($testCases as $data) {
        $validator = Validator::make($data, $request->rules());
        expect($validator->fails())->toBeFalse()
            ->and($validator->errors()->all())->toBeEmpty();
    }
});

it('fails validation with invalid data', function () {
    // Test cases
    $testCases = [
        'missing_title' => [
            'data' => [
                'description' => 'Job description without title',
                'application_process' => ApplicationProcess::EMAIL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['title'],
        ],
        'missing_description' => [
            'data' => [
                'title' => 'Job without description',
                'application_process' => ApplicationProcess::EMAIL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['description'],
        ],
        'invalid_workload_range' => [
            'data' => [
                'title' => 'Invalid Workload Job',
                'description' => 'Job with invalid workload range',
                'workload_min' => 80,
                'workload_max' => 50, // Max less than min
                'application_process' => ApplicationProcess::EMAIL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['workload_max'],
        ],
        'invalid_experience_range' => [
            'data' => [
                'title' => 'Invalid Experience Job',
                'description' => 'Job with invalid experience range',
                'experience_years_min' => 10,
                'experience_years_max' => 5, // Max less than min
                'application_process' => ApplicationProcess::EMAIL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['experience_years_max'],
        ],
        'invalid_salary_range' => [
            'data' => [
                'title' => 'Invalid Salary Job',
                'description' => 'Job with invalid salary range',
                'no_salary' => false,
                'salary_min' => 100000,
                'salary_max' => 80000, // Max less than min
                'application_process' => ApplicationProcess::EMAIL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['salary_max'],
        ],
        'invalid_email' => [
            'data' => [
                'title' => 'Invalid Email Job',
                'description' => 'Job with invalid email',
                'application_email' => 'not-an-email',
                'application_process' => ApplicationProcess::EMAIL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['application_email'],
        ],
        'invalid_url' => [
            'data' => [
                'title' => 'Invalid URL Job',
                'description' => 'Job with invalid URL',
                'application_url' => 'not-a-url',
                'application_process' => ApplicationProcess::URL,
                'status' => JobStatus::DRAFT,
            ],
            'expectedErrors' => ['application_url'],
        ],
    ];

    $request = new UpdateJobListingRequest();

    foreach ($testCases as $name => $testCase) {
        $validator = Validator::make($testCase['data'], $request->rules());
        expect($validator->fails())->toBeTrue();

        foreach ($testCase['expectedErrors'] as $field) {
            expect($validator->errors()->has($field))->toBeTrue();
        }
    }
});
