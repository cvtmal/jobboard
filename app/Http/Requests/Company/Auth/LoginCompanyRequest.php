<?php

declare(strict_types=1);

namespace App\Http\Requests\Company\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class LoginCompanyRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'lowercase'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }
}
