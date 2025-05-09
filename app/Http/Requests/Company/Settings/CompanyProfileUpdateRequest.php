<?php

declare(strict_types=1);

namespace App\Http\Requests\Company\Settings;

use App\Models\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CompanyProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $userId = null;

        // Ensure we have a valid Company user with an ID
        if ($user instanceof Company && $user->id) {
            $userId = $user->id;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Company::class)->ignore($userId),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'url' => ['nullable', 'url', 'max:255'],
            'size' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'description_english' => ['nullable', 'string', 'max:10000'],
            'description_german' => ['nullable', 'string', 'max:10000'],
            'description_french' => ['nullable', 'string', 'max:10000'],
            'description_italian' => ['nullable', 'string', 'max:10000'],
            'logo' => ['nullable', 'string', 'max:255'],
            'cover' => ['nullable', 'string', 'max:255'],
            'video' => ['nullable', 'string', 'max:255'],
            'newsletter' => ['nullable', 'boolean'],
        ];
    }
}
