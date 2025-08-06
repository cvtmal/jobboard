<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use App\Models\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CareerPageUpdateRequest extends FormRequest
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

        if ($user instanceof Company && $user->id) {
            $userId = $user->id;
        }

        return [
            'career_page_enabled' => ['boolean'],
            'career_page_slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique(Company::class)->ignore($userId),
            ],
            'career_page_image' => [
                'nullable',
                'file',
                'image',
                'max:3584',
                'mimes:jpeg,jpg,png,gif,webp',
                'dimensions:min_width=752,min_height=480',
            ],
            'career_page_videos' => ['nullable', 'array', 'max:5'],
            'career_page_videos.*' => ['string', 'max:500'],
            'career_page_domain' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/',
                Rule::unique(Company::class)->ignore($userId),
            ],
            'spontaneous_application_enabled' => ['boolean'],
            'career_page_visibility' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'career_page_slug.regex' => 'The career page slug may only contain letters, numbers, and hyphens.',
            'career_page_slug.unique' => 'This career page slug is already taken.',
            'career_page_image.file' => 'The career page image must be a valid file.',
            'career_page_image.image' => 'The file must be an image.',
            'career_page_image.max' => 'The image must not be larger than 3.5MB.',
            'career_page_image.mimes' => 'The image must be in JPEG, PNG, GIF, or WebP format.',
            'career_page_image.dimensions' => 'The image must be at least 752x480 pixels.',
            'career_page_videos.max' => 'You may not add more than 5 videos.',
            'career_page_domain.regex' => 'Please enter a valid domain name.',
            'career_page_domain.unique' => 'This domain is already in use by another company.',
        ];
    }
}
