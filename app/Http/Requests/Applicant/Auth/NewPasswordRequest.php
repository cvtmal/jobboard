<?php

declare(strict_types=1);

namespace App\Http\Requests\Applicant\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

final class NewPasswordRequest extends FormRequest
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
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email', 'lowercase'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
