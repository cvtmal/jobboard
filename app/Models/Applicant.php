<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ApplicantFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, JobApplication> $jobApplications
 * @property-read int|null $job_applications_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 *
 * @method static ApplicantFactory factory($count = null, $state = [])
 * @method static Builder<static>|Applicant newModelQuery()
 * @method static Builder<static>|Applicant newQuery()
 * @method static Builder<static>|Applicant query()
 * @method static Builder<static>|Applicant whereCreatedAt($value)
 * @method static Builder<static>|Applicant whereEmail($value)
 * @method static Builder<static>|Applicant whereEmailVerifiedAt($value)
 * @method static Builder<static>|Applicant whereId($value)
 * @method static Builder<static>|Applicant whereName($value)
 * @method static Builder<static>|Applicant wherePassword($value)
 * @method static Builder<static>|Applicant whereRememberToken($value)
 * @method static Builder<static>|Applicant whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Applicant extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<ApplicantFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the job applications associated with the applicant.
     *
     * @return HasMany<JobApplication, $this>
     */
    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

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
}
