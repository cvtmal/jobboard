<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CompanyFactory;
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
 * @property string|null $address
 * @property string|null $postcode
 * @property string|null $city
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $url
 * @property string|null $size
 * @property string|null $type
 * @property string|null $description_german
 * @property string|null $description_english
 * @property string|null $description_french
 * @property string|null $description_italian
 * @property string|null $logo
 * @property string|null $cover
 * @property string|null $video
 * @property bool|null $newsletter
 * @property string|null $internal_notes
 * @property bool $active
 * @property bool $blocked
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, JobListing> $jobs
 * @property-read int|null $jobs_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 *
 * @method static CompanyFactory factory($count = null, $state = [])
 * @method static Builder<static>|Company newModelQuery()
 * @method static Builder<static>|Company newQuery()
 * @method static Builder<static>|Company query()
 * @method static Builder<static>|Company whereActive($value)
 * @method static Builder<static>|Company whereAddress($value)
 * @method static Builder<static>|Company whereBlocked($value)
 * @method static Builder<static>|Company whereCity($value)
 * @method static Builder<static>|Company whereCover($value)
 * @method static Builder<static>|Company whereCreatedAt($value)
 * @method static Builder<static>|Company whereDescriptionEnglish($value)
 * @method static Builder<static>|Company whereDescriptionFrench($value)
 * @method static Builder<static>|Company whereDescriptionGerman($value)
 * @method static Builder<static>|Company whereDescriptionItalian($value)
 * @method static Builder<static>|Company whereEmail($value)
 * @method static Builder<static>|Company whereEmailVerifiedAt($value)
 * @method static Builder<static>|Company whereId($value)
 * @method static Builder<static>|Company whereInternalNotes($value)
 * @method static Builder<static>|Company whereLatitude($value)
 * @method static Builder<static>|Company whereLogo($value)
 * @method static Builder<static>|Company whereLongitude($value)
 * @method static Builder<static>|Company whereName($value)
 * @method static Builder<static>|Company whereNewsletter($value)
 * @method static Builder<static>|Company wherePassword($value)
 * @method static Builder<static>|Company wherePostcode($value)
 * @method static Builder<static>|Company whereRememberToken($value)
 * @method static Builder<static>|Company whereSize($value)
 * @method static Builder<static>|Company whereType($value)
 * @method static Builder<static>|Company whereUpdatedAt($value)
 * @method static Builder<static>|Company whereUrl($value)
 * @method static Builder<static>|Company whereVideo($value)
 *
 * @mixin Eloquent
 */
final class Company extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the jobs listed by this company.
     *
     * @return HasMany<JobListing, $this>
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(JobListing::class);
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
            'latitude' => 'float',
            'longitude' => 'float',
            'newsletter' => 'boolean',
            'active' => 'boolean',
            'blocked' => 'boolean',
        ];
    }
}
