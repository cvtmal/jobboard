<?php

declare(strict_types=1);

namespace App\Auth\Providers;

use App\Models\Company;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class CompanyUserProvider extends EloquentUserProvider
{
    /**
     * Create a new company user provider.
     */
    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, Company::class);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if ($credentials === [] ||
            (count($credentials) === 1 &&
             array_key_exists('password', $credentials))) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.

        // Create query and explicitly specify the model type
        /** @var Builder<Company> $query */
        $query = Company::query();

        foreach ($credentials as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            if ($value instanceof Arrayable) {
                continue;
            }
            if ($key === 'password') {
                continue;
            }

            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (! $user instanceof Company) {
            return false;
        }

        $plain = $credentials['password'] ?? '';

        // Ensure we have a string password before checking
        if (! is_string($plain)) {
            return false;
        }

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
