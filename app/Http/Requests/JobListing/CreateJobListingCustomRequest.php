<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\JobCategory;
use App\Enums\JobStatus;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class CreateJobListingCustomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'title' => ['required', 'string', 'max:255'],
            'company_description' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'requirements' => ['required', 'string'],
            'benefits' => ['nullable', 'string'],
            'final_words' => ['nullable', 'string'],

            // Location
            'workplace' => ['required', new Enum(Workplace::class)],
            'office_location' => ['required', 'string', 'max:255'],

            // Job details
            'application_language' => ['required', 'string', 'in:english,german,french,italian'],
            'category' => ['required', new Enum(JobCategory::class)],
            'employment_type' => ['required', 'string', 'in:employee,interim,apprenticeship,internship,working_student,traineeship,side_job,freelance'],
            'seniority_level' => ['nullable', 'string', 'in:no_experience,junior,mid_level,professional,senior,lead'],

            // Salary
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_period' => ['nullable', 'string', 'in:hourly,daily,weekly,monthly,yearly'],

            // Skills
            'skills' => ['nullable', 'string'],

            // Hidden fields
            'application_process' => ['required', new Enum(ApplicationProcess::class)],
            'status' => ['required', new Enum(JobStatus::class)],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Map custom employment type to standard EmploymentType enum
        if ($this->has('employment_type')) {
            $mappedType = match ($this->input('employment_type')) {
                'employee' => EmploymentType::FULL_TIME->value,
                'interim' => EmploymentType::TEMPORARY->value,
                'apprenticeship' => EmploymentType::INTERNSHIP->value,
                'internship' => EmploymentType::INTERNSHIP->value,
                'working_student' => EmploymentType::PART_TIME->value,
                'traineeship' => EmploymentType::INTERNSHIP->value,
                'side_job' => EmploymentType::PART_TIME->value,
                'freelance' => EmploymentType::CONTRACT->value,
                default => EmploymentType::FULL_TIME->value,
            };

            $this->merge([
                'employment_type_mapped' => $mappedType,
            ]);
        }

        // Map salary period to SalaryType
        if ($this->has('salary_period')) {
            $mappedSalaryType = match ($this->input('salary_period')) {
                'hourly' => SalaryType::HOURLY->value,
                'daily' => SalaryType::DAILY->value,
                'weekly' => SalaryType::MONTHLY->value, // No weekly type, so map to monthly
                'monthly' => SalaryType::MONTHLY->value,
                'yearly' => SalaryType::YEARLY->value,
                default => SalaryType::YEARLY->value,
            };

            $this->merge([
                'salary_type' => $mappedSalaryType,
            ]);
        }
    }
}
