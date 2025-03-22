<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmploymentType;
use App\Enums\Workplace;
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
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string|null $mobile_phone
 * @property string|null $headline
 * @property string|null $bio
 * @property bool $work_permit
 * @property EmploymentType|null $employment_type_preference
 * @property Workplace|null $workplace_preference
 * @property CarbonImmutable|null $available_from
 * @property float|null $salary_expectation
 * @property string|null $resume_path
 * @property string|null $profile_photo_path
 * @property string|null $portfolio_url
 * @property string|null $linkedin_url
 * @property string|null $github_url
 * @property string|null $website_url
 * @property CarbonImmutable|null $date_of_birth
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal_code
 * @property string|null $country
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
 * @method static Builder<static>|Applicant whereFirstName($value)
 * @method static Builder<static>|Applicant whereLastName($value)
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
     * Get the full name of the applicant.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
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
            'work_permit' => 'boolean',
            'employment_type_preference' => EmploymentType::class,
            'workplace_preference' => Workplace::class,
            'available_from' => 'date',
            'date_of_birth' => 'date',
            'salary_expectation' => 'decimal:2',
        ];
    }
}
