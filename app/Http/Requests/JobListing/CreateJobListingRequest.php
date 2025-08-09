<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobCategory;
use App\Enums\JobStatus;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class CreateJobListingRequest extends FormRequest
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
            'categories' => ['nullable', 'array'],
            'categories.*' => ['required', new Enum(JobCategory::class)],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'employment_type' => ['nullable', new Enum(EmploymentType::class)],
            'workload_min' => ['nullable', 'integer', 'min:1', 'max:100'],
            'workload_max' => ['nullable', 'integer', 'min:1', 'max:100', 'gte:workload_min'],
            'active_from' => ['nullable', 'date'],
            'active_until' => ['nullable', 'date', 'after_or_equal:active_from'],
            'workplace' => ['nullable', new Enum(Workplace::class)],
            'hierarchy' => ['nullable', 'string', 'max:255'],
            'experience_level' => ['nullable', new Enum(ExperienceLevel::class)],
            'experience_years_min' => ['nullable', 'integer', 'min:0'],
            'experience_years_max' => ['nullable', 'integer', 'min:0', 'gte:experience_years_min'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string'],
            'address' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'no_salary' => ['boolean'],
            'salary_type' => ['nullable', new Enum(SalaryType::class)],
            'salary_option' => ['nullable', new Enum(SalaryOption::class)],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'max:3'],
            'job_tier' => ['nullable', 'string', 'max:255'],
            'job_tier_id' => ['nullable', 'integer', 'exists:job_tiers,id'],
            'application_process' => ['required', new Enum(ApplicationProcess::class)],
            'application_email' => ['nullable', 'email', 'max:255'],
            'application_url' => ['nullable', 'url', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'internal_notes' => ['nullable', 'string'],
            'status' => ['required', new Enum(JobStatus::class)],
        ];
    }
}
