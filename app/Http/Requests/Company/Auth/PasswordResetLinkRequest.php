<?php

declare(strict_types=1);

namespace App\Http\Requests\Company\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class PasswordResetLinkRequest extends FormRequest
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
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'lowercase'],
        ];
    }
}
