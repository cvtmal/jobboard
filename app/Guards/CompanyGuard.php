<?php

declare(strict_types=1);

namespace App\Guards;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Session\Session;

final class CompanyGuard extends SessionGuard
{
    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array{email?: string, password?: string}  $credentials
     * @param  bool  $remember
     */
    public function attempt(array $credentials = [], $remember = false): bool
    {
        $this->fireAttemptEvent($credentials, $remember);

        $user = $this->provider->retrieveByCredentials($credentials);

        // Only set lastAttempted if the user is not null
        if ($user instanceof Authenticatable) {
            $this->lastAttempted = $user;
        } else {
            return false;
        }

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

    /**
     * Log the user out of the application.
     * 
     * Overriding to ensure we forcefully clear session data
     * and remember tokens to prevent authentication persistence.
     */
    public function logout(): void
    {
        $user = $this->user();

        // Clear the user's remember token if it exists
        if ($this->hasUser()) {
            $this->cycleRememberToken($user);
            $this->clearUserDataFromStorage();
        }

        // Ensure the remember cookie is properly deleted
        $this->getCookieJar()->queue(
            $this->getCookieJar()->forget($this->getRecallerName())
        );

        // Explicitly unset the user
        $this->user = null;
    }
}
