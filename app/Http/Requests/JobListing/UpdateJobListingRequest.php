<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class UpdateJobListingRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'workload_min' => ['required', 'numeric', 'min:0'],
            'workload_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:workload_min'],
            'description_and_requirements' => ['required', 'string', 'min:20', 'max:4000'],
            'benefits' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],

            // Location
            'workplace' => ['required', new Enum(Workplace::class)],
            'office_location' => ['required', 'string', 'max:255'],

            // Job details
            'employment_type' => ['required', 'string', 'in:permanent,temporary,freelance,internship,side-job,apprenticeship,working-student,interim'],
            'seniority_level' => ['nullable', 'string', 'in:no_experience,junior,mid_level,professional,senior,lead'],

            // Salary
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_period' => ['nullable', 'string', 'in:hourly,daily,weekly,monthly,yearly'],

            // Skills
            'skills' => ['nullable', 'string'],

            // Screening data (Step 4)
            'application_documents' => ['nullable', 'array'],
            'application_documents.cv' => ['nullable', 'string', 'in:required,optional,hidden'],
            'application_documents.cover_letter' => ['required_with:application_documents', 'string', 'in:required,optional,hidden'],
            'screening_questions' => ['nullable', 'array'],
            'screening_questions.*.id' => ['required_with:screening_questions.*', 'string'],
            'screening_questions.*.text' => ['required_with:screening_questions.*', 'string'],
            'screening_questions.*.requirement' => ['required_with:screening_questions.*', 'string', 'in:optional,required,knockout'],
            'screening_questions.*.answerType' => ['required_with:screening_questions.*', 'string', 'in:yes/no,single-choice,multiple-choice,date,number,file-upload,short-text'],
            'screening_questions.*.choices' => ['nullable', 'array'],
            'screening_questions.*.choices.*' => ['string'],

            // Application process fields
            'application_process' => ['required', 'string', 'in:email,url,both'],
            'application_email' => ['required_if:application_process,email', 'required_if:application_process,both', 'nullable', 'email'],
            'application_url' => ['required_if:application_process,url', 'required_if:application_process,both', 'nullable', 'string'],

            // Hidden fields
            'status' => ['required', new Enum(JobStatus::class)],

            // Package selection (step 5)
            'selected_tier_id' => ['nullable', 'integer', 'exists:job_tiers,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation(): void
    {
        // Map custom employment type to standard EmploymentType enum
        if ($this->has('employment_type')) {
            $mappedType = match ($this->input('employment_type')) {
                'permanent' => EmploymentType::PERMANENT->value,
                'temporary' => EmploymentType::TEMPORARY->value,
                'freelance' => EmploymentType::FREELANCE->value,
                'internship' => EmploymentType::INTERNSHIP->value,
                'side-job' => EmploymentType::SIDE_JOB->value,
                'apprenticeship' => EmploymentType::APPRENTICESHIP->value,
                'working-student' => EmploymentType::WORKING_STUDENT->value,
                'interim' => EmploymentType::INTERIM->value,
                default => EmploymentType::PERMANENT->value,
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

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'application_process.required' => 'Please select an application method.',
            'application_email.required_if' => 'Email address is required when email application method is selected.',
            'application_url.required_if' => 'Website URL is required when URL application method is selected.',
        ];
    }
}
