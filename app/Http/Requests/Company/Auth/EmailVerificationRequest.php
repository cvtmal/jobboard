<?php

declare(strict_types=1);

namespace App\Http\Requests\Company\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class EmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (! hash_equals((string) $this->route('id'), (string) $this->user('company')?->getKey())) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'), sha1($this->user('company')?->getEmailForVerification() ?? ''))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
