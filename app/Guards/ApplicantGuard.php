<?php

declare(strict_types=1);

namespace App\Guards;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Session\Session;

final class ApplicantGuard extends SessionGuard
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

        if ($user instanceof Authenticatable) {
            $this->lastAttempted = $user;
        } else {
            return false;
        }

        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user, $remember);

            return true;
        }

        $this->fireFailedEvent($user, $credentials);

        return false;
    }

    /**
     * Get the session store used by the guard.
     * Overriding the parent method to align with preference
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
        if ($this->hasUser() && $user instanceof Authenticatable) {
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
