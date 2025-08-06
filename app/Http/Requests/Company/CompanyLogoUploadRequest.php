<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Dimensions;

final class CompanyLogoUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('company') !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'logo' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'min:1', // 1KB minimum
                'max:'.(8 * 1024), // 8MB maximum
                new Dimensions([
                    'min_width' => 320,
                    'min_height' => 320,
                    'ratio' => 1, // Square aspect ratio (1:1)
                ]),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.required' => __('Das Logo ist erforderlich.'),
            'logo.image' => __('Das Logo muss eine gültige Bilddatei sein.'),
            'logo.min' => __('Das Logo muss mindestens 1KB groß sein.'),
            'logo.max' => __('Das Logo darf nicht größer als 8MB sein.'),
            'logo.dimensions' => __('Das Logo muss mindestens 320x320 Pixel groß sein.'),
            'logo.mimes' => __('Das Logo muss eine PNG- oder JPG-Datei sein.'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'logo' => __('Logo'),
        ];
    }
}
