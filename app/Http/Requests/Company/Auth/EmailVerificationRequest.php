<?php

declare(strict_types=1);

namespace App\Http\Requests\Company\Auth;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

final class EmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Validate company user
        $user = $this->user('company');
        if (! $user instanceof Company) {
            return false;
        }

        // Gather required verification data
        $routeId = $this->route('id');
        $userId = $user->getKey();
        $routeHash = $this->route('hash');
        $email = $user->getEmailForVerification();

        // Ensure we have all required values before proceeding
        if (! $this->hasRequiredValues($routeId, $userId, $routeHash, $email)) {
            return false;
        }

        // Verify user ID matches route ID
        if (! $this->idsMatch($routeId, $userId)) {
            return false;
        }

        // Verify hash matches expected value for email
        return $this->hashIsValid($routeHash, $email);
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

    /**
     * Check if all required values are present.
     */
    private function hasRequiredValues(mixed $routeId, mixed $userId, mixed $routeHash, mixed $email): bool
    {
        // Check if any value is null
        return ! ($routeId === null || $userId === null || $routeHash === null || $email === null);
    }

    /**
     * Check if IDs match when converted to strings.
     */
    private function idsMatch(mixed $routeId, mixed $userId): bool
    {
        // Convert to strings when possible
        $routeIdStr = $this->toStringOrEmpty($routeId);
        $userIdStr = $this->toStringOrEmpty($userId);

        // Compare as strings
        return $routeIdStr !== '' && $userIdStr !== '' && $routeIdStr === $userIdStr;
    }

    /**
     * Verify email hash is valid.
     */
    private function hashIsValid(mixed $hash, mixed $email): bool
    {
        // Convert to strings when possible
        $hashStr = $this->toStringOrEmpty($hash);
        $emailStr = $this->toStringOrEmpty($email);

        // Both values must be non-empty
        if ($hashStr === '' || $emailStr === '') {
            return false;
        }

        // Generate correct hash using SHA-1 which is Laravel's standard approach
        $correctHash = sha1($emailStr);

        // Use constant-time comparison to prevent timing attacks
        return hash_equals($hashStr, $correctHash);
    }

    /**
     * Convert a value to string or empty string if not possible.
     */
    private function toStringOrEmpty(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
