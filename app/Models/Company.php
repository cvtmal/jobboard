<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Job;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class Company extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the jobs listed by this company.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
