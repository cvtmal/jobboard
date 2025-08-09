<?php

declare(strict_types=1);

namespace App\Http\Requests\Company\Settings;

use App\Models\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'url' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'industry' => ['nullable', 'string', 'max:100'],
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
