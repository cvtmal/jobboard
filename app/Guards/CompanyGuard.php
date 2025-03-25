<?php

declare(strict_types=1);

namespace App\Guards;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Session\Session;

final class CompanyGuard extends SessionGuard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array{email: string, password: string}  $credentials
     * @param  bool  $remember
     */
    public function attempt(array $credentials = [], $remember = false): bool
    {
        $this->fireAttemptEvent($credentials, $remember);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, $remember);

            return true;
        }

        // If the authentication attempt fails we will fire an event so that the user
        // may be notified of any suspicious attempts to access their account from
        // an unrecognized IP address or a strange location.
        $this->fireFailedEvent($user, $credentials);

        return false;
    }

    /**
     * Get the session store used by the guard.
     * Overriding the parent method to align with my preference
     * for strict typing with explicit return type.
     */
    public function getSession(): Session
    {
        return $this->session;
    }
}
