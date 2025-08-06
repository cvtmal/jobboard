<?php

declare(strict_types=1);

namespace App\Http\Requests\JobListing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Dimensions;

final class JobListingBannerUploadRequest extends FormRequest
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
            'banner' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'min:1', // 1KB minimum
                'max:'.(16 * 1024), // 16MB maximum
                new Dimensions([
                    'min_width' => 1200,
                    'min_height' => 400,
                    'ratio' => 3, // 3:1 aspect ratio
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
            'banner.required' => __('Das Banner ist erforderlich.'),
            'banner.image' => __('Das Banner muss eine gültige Bilddatei sein.'),
            'banner.min' => __('Das Banner muss mindestens 1KB groß sein.'),
            'banner.max' => __('Das Banner darf nicht größer als 16MB sein.'),
            'banner.dimensions' => __('Das Banner muss mindestens 1200x400 Pixel groß sein und ein 3:1 Seitenverhältnis haben.'),
            'banner.mimes' => __('Das Banner muss eine PNG- oder JPG-Datei sein.'),
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
            'banner' => __('Banner'),
        ];
    }
}
