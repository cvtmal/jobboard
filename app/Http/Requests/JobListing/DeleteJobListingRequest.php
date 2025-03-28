<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use Illuminate\Foundation\Http\FormRequest;

final class DeleteJobListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Add authorization logic if needed
        // For example, check if the user owns the job listing or has permission to delete it
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
