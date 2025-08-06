<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\Company\VerifyEmail;
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
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $phone_number
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
 * @property string|null $logo_path
 * @property string|null $logo_original_name
 * @property int|null $logo_file_size
 * @property string|null $logo_mime_type
 * @property array<string, int>|null $logo_dimensions
 * @property CarbonImmutable|null $logo_uploaded_at
 * @property string|null $banner_path
 * @property string|null $banner_original_name
 * @property int|null $banner_file_size
 * @property string|null $banner_mime_type
 * @property array<string, int>|null $banner_dimensions
 * @property CarbonImmutable|null $banner_uploaded_at
 * @property string|null $video
 * @property bool|null $newsletter
 * @property string|null $internal_notes
 * @property bool $active
 * @property bool $blocked
 * @property bool $profile_completed
 * @property CarbonImmutable|null $profile_completed_at
 * @property array<string, bool>|null $profile_completion_steps
 * @property string|null $industry
 * @property int|null $founded_year
 * @property string|null $mission_statement
 * @property array<string>|null $benefits
 * @property array<string>|null $company_culture
 * @property bool $career_page_enabled
 * @property string|null $career_page_slug
 * @property string|null $career_page_image
 * @property array<array{id: string, url: string, title: string}>|null $career_page_videos
 * @property string|null $career_page_domain
 * @property bool $spontaneous_application_enabled
 * @property bool $career_page_visibility
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
 * @method static Builder<static>|Company whereBannerPath($value)
 * @method static Builder<static>|Company whereBannerOriginalName($value)
 * @method static Builder<static>|Company whereBannerFileSize($value)
 * @method static Builder<static>|Company whereBannerMimeType($value)
 * @method static Builder<static>|Company whereBannerDimensions($value)
 * @method static Builder<static>|Company whereBannerUploadedAt($value)
 * @method static Builder<static>|Company whereCreatedAt($value)
 * @method static Builder<static>|Company whereDescriptionEnglish($value)
 * @method static Builder<static>|Company whereDescriptionFrench($value)
 * @method static Builder<static>|Company whereDescriptionGerman($value)
 * @method static Builder<static>|Company whereDescriptionItalian($value)
 * @method static Builder<static>|Company whereEmail($value)
 * @method static Builder<static>|Company whereEmailVerifiedAt($value)
 * @method static Builder<static>|Company whereFirstName($value)
 * @method static Builder<static>|Company whereId($value)
 * @method static Builder<static>|Company whereInternalNotes($value)
 * @method static Builder<static>|Company whereLastName($value)
 * @method static Builder<static>|Company whereLatitude($value)
 * @method static Builder<static>|Company whereLogoPath($value)
 * @method static Builder<static>|Company whereLogoOriginalName($value)
 * @method static Builder<static>|Company whereLogoFileSize($value)
 * @method static Builder<static>|Company whereLogoMimeType($value)
 * @method static Builder<static>|Company whereLogoDimensions($value)
 * @method static Builder<static>|Company whereLogoUploadedAt($value)
 * @method static Builder<static>|Company whereLongitude($value)
 * @method static Builder<static>|Company whereName($value)
 * @method static Builder<static>|Company whereNewsletter($value)
 * @method static Builder<static>|Company wherePassword($value)
 * @method static Builder<static>|Company wherePhoneNumber($value)
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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'postcode',
        'city',
        'latitude',
        'longitude',
        'url',
        'size',
        'type',
        'industry',
        'founded_year',
        'description_german',
        'description_english',
        'description_french',
        'description_italian',
        'mission_statement',
        'benefits',
        'company_culture',
        'logo_path',
        'logo_original_name',
        'logo_file_size',
        'logo_mime_type',
        'logo_dimensions',
        'logo_uploaded_at',
        'banner_path',
        'banner_original_name',
        'banner_file_size',
        'banner_mime_type',
        'banner_dimensions',
        'banner_uploaded_at',
        'video',
        'newsletter',
        'internal_notes',
        'active',
        'blocked',
        'profile_completed',
        'profile_completed_at',
        'profile_completion_steps',
        'career_page_enabled',
        'career_page_slug',
        'career_page_image',
        'career_page_videos',
        'career_page_domain',
        'spontaneous_application_enabled',
        'career_page_visibility',
        'email',
        'email_verified_at',
        'password',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'logo_url',
        'banner_url',
        'career_page_image_url',
    ];

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
     * Get the job listings by this company (alias for jobs).
     *
     * @return HasMany<JobListing, $this>
     */
    public function jobListings(): HasMany
    {
        return $this->jobs();
    }

    /**
     * Determine if the company has verified their email address.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given company's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'latitude' => 'float',
            'longitude' => 'float',
            'newsletter' => 'boolean',
            'active' => 'boolean',
            'blocked' => 'boolean',
            'profile_completed' => 'boolean',
            'profile_completed_at' => 'datetime',
            'profile_completion_steps' => 'array',
            'founded_year' => 'integer',
            'benefits' => 'array',
            'company_culture' => 'array',
            'logo_dimensions' => 'array',
            'logo_uploaded_at' => 'datetime',
            'banner_dimensions' => 'array',
            'banner_uploaded_at' => 'datetime',
            'career_page_enabled' => 'boolean',
            'career_page_videos' => 'array',
            'spontaneous_application_enabled' => 'boolean',
            'career_page_visibility' => 'boolean',
        ];
    }

    /**
     * Get the full URL for the company logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    /**
     * Get the full URL for the company banner.
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (! $this->banner_path) {
            return null;
        }

        return Storage::disk('public')->url($this->banner_path);
    }

    /**
     * Get the full URL for the career page image.
     */
    public function getCareerPageImageUrlAttribute(): ?string
    {
        if (! $this->career_page_image) {
            return null;
        }

        // If it's already a full URL, return as is
        if (str_starts_with($this->career_page_image, 'http')) {
            return $this->career_page_image;
        }

        return Storage::disk('public')->url($this->career_page_image);
    }

    /**
     * Check if the company has a logo uploaded.
     */
    public function hasLogo(): bool
    {
        return ! is_null($this->logo_path) && Storage::disk('public')->exists($this->logo_path);
    }

    /**
     * Check if the company has a banner uploaded.
     */
    public function hasBanner(): bool
    {
        return ! is_null($this->banner_path) && Storage::disk('public')->exists($this->banner_path);
    }

    /**
     * Check if the company has a career page image uploaded.
     */
    public function hasCareerPageImage(): bool
    {
        if (! $this->career_page_image) {
            return false;
        }

        // If it's a full URL, we can't check if it exists locally
        if (str_starts_with($this->career_page_image, 'http')) {
            return true;
        }

        return Storage::disk('public')->exists($this->career_page_image);
    }

    /**
     * Get the logo file size in human readable format.
     */
    public function getLogoFileSizeFormattedAttribute(): ?string
    {
        if (! $this->logo_file_size) {
            return null;
        }

        return $this->formatFileSize($this->logo_file_size);
    }

    /**
     * Get the banner file size in human readable format.
     */
    public function getBannerFileSizeFormattedAttribute(): ?string
    {
        if (! $this->banner_file_size) {
            return null;
        }

        return $this->formatFileSize($this->banner_file_size);
    }

    /**
     * Delete the company logo file from storage.
     */
    public function deleteLogo(): bool
    {
        if (! $this->logo_path) {
            return true;
        }

        $deleted = Storage::disk('public')->delete($this->logo_path);

        if ($deleted) {
            $this->update([
                'logo_path' => null,
                'logo_original_name' => null,
                'logo_file_size' => null,
                'logo_mime_type' => null,
                'logo_dimensions' => null,
                'logo_uploaded_at' => null,
            ]);
        }

        return $deleted;
    }

    /**
     * Delete the company banner file from storage.
     */
    public function deleteBanner(): bool
    {
        if (! $this->banner_path) {
            return true;
        }

        $deleted = Storage::disk('public')->delete($this->banner_path);

        if ($deleted) {
            $this->update([
                'banner_path' => null,
                'banner_original_name' => null,
                'banner_file_size' => null,
                'banner_mime_type' => null,
                'banner_dimensions' => null,
                'banner_uploaded_at' => null,
            ]);
        }

        return $deleted;
    }

    /**
     * Delete the company career page image file from storage.
     */
    public function deleteCareerPageImage(): bool
    {
        if (! $this->career_page_image) {
            return true;
        }

        // Only delete if it's a local file (not a full URL)
        if (str_starts_with($this->career_page_image, 'http')) {
            $this->update(['career_page_image' => null]);

            return true;
        }

        $deleted = Storage::disk('public')->delete($this->career_page_image);

        if ($deleted) {
            $this->update(['career_page_image' => null]);
        }

        return $deleted;
    }

    /**
     * Check if company has completed mandatory fields.
     */
    public function hasMandatoryFields(): bool
    {
        return ! empty($this->name) && ! empty($this->email);
    }

    /**
     * Check if onboarding should be shown (for recently registered companies).
     */
    public function shouldShowOnboarding(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(30));
    }

    /**
     * Format file size in human readable format.
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= 1024 ** $pow;

        return round($bytes, 2).' '.$units[$pow];
    }
}
