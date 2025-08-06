<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobCategory;
use App\Enums\JobStatus;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\SwissCanton;
use App\Enums\SwissRegion;
use App\Enums\SwissSubRegion;
use App\Enums\Workplace;
use Carbon\CarbonImmutable;
use Database\Factories\JobListingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $company_id
 * @property string|null $reference_number
 * @property string $title
 * @property string $description
 * @property EmploymentType|null $employment_type
 * @property int|null $workload_min
 * @property int|null $workload_max
 * @property CarbonImmutable|null $active_from
 * @property CarbonImmutable|null $active_until
 * @property Workplace|null $workplace
 * @property string|null $hierarchy
 * @property ExperienceLevel|null $experience_level
 * @property int|null $experience_years_min
 * @property int|null $experience_years_max
 * @property string|null $education_level
 * @property array<array-key, mixed>|null $languages
 * @property string|null $address
 * @property string|null $postcode
 * @property string|null $city
 * @property SwissCanton|null $primary_canton_code
 * @property SwissSubRegion|null $primary_sub_region
 * @property float|null $primary_latitude
 * @property float|null $primary_longitude
 * @property bool $has_multiple_locations
 * @property bool $allows_remote
 * @property bool $no_salary
 * @property SalaryType|null $salary_type
 * @property SalaryOption|null $salary_option
 * @property numeric|null $salary_min
 * @property numeric|null $salary_max
 * @property string|null $salary_currency
 * @property string|null $job_tier
 * @property ApplicationProcess $application_process
 * @property string|null $application_email
 * @property string|null $application_url
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string|null $internal_notes
 * @property bool $use_company_logo
 * @property bool $use_company_banner
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
 * @property JobStatus $status
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property int|null $job_tier_id
 * @property-read Collection<int, JobApplication> $applications
 * @property-read int|null $applications_count
 * @property-read Company $company
 * @property-read JobTier|null $jobTier
 * @property-read Collection<int, JobListingAdditionalLocation> $additionalLocations
 * @property-read Collection<int, Skill> $skills
 *
 * @method static JobListingFactory factory($count = null, $state = [])
 * @method static Builder<static>|JobListing newModelQuery()
 * @method static Builder<static>|JobListing newQuery()
 * @method static Builder<static>|JobListing query()
 * @method static Builder<static>|JobListing whereActiveFrom($value)
 * @method static Builder<static>|JobListing whereActiveUntil($value)
 * @method static Builder<static>|JobListing whereAddress($value)
 * @method static Builder<static>|JobListing whereApplicationEmail($value)
 * @method static Builder<static>|JobListing whereApplicationProcess($value)
 * @method static Builder<static>|JobListing whereApplicationUrl($value)
 * @method static Builder<static>|JobListing whereCity($value)
 * @method static Builder<static>|JobListing whereCompanyId($value)
 * @method static Builder<static>|JobListing whereContactEmail($value)
 * @method static Builder<static>|JobListing whereContactPerson($value)
 * @method static Builder<static>|JobListing whereCreatedAt($value)
 * @method static Builder<static>|JobListing whereDescription($value)
 * @method static Builder<static>|JobListing whereEducationLevel($value)
 * @method static Builder<static>|JobListing whereEmploymentType($value)
 * @method static Builder<static>|JobListing whereExperienceLevel($value)
 * @method static Builder<static>|JobListing whereExperienceYearsMax($value)
 * @method static Builder<static>|JobListing whereExperienceYearsMin($value)
 * @method static Builder<static>|JobListing whereHierarchy($value)
 * @method static Builder<static>|JobListing whereId($value)
 * @method static Builder<static>|JobListing whereInternalNotes($value)
 * @method static Builder<static>|JobListing whereJobTier($value)
 * @method static Builder<static>|JobListing whereJobTierId($value)
 * @method static Builder<static>|JobListing whereLanguages($value)
 * @method static Builder<static>|JobListing whereNoSalary($value)
 * @method static Builder<static>|JobListing wherePostcode($value)
 * @method static Builder<static>|JobListing whereReferenceNumber($value)
 * @method static Builder<static>|JobListing whereSalaryCurrency($value)
 * @method static Builder<static>|JobListing whereSalaryMax($value)
 * @method static Builder<static>|JobListing whereSalaryMin($value)
 * @method static Builder<static>|JobListing whereSalaryOption($value)
 * @method static Builder<static>|JobListing whereSalaryType($value)
 * @method static Builder<static>|JobListing whereStatus($value)
 * @method static Builder<static>|JobListing whereTitle($value)
 * @method static Builder<static>|JobListing whereUpdatedAt($value)
 * @method static Builder<static>|JobListing whereWorkloadMax($value)
 * @method static Builder<static>|JobListing whereWorkloadMin($value)
 * @method static Builder<static>|JobListing whereWorkplace($value)
 * @method static Builder<static>|JobListing wherePrimaryCantonCode($value)
 * @method static Builder<static>|JobListing wherePrimaryLatitude($value)
 * @method static Builder<static>|JobListing wherePrimaryLongitude($value)
 * @method static Builder<static>|JobListing whereHasMultipleLocations($value)
 * @method static Builder<static>|JobListing whereAllowsRemote($value)
 *
 * @mixin Eloquent
 */
final class JobListing extends Model
{
    /** @use HasFactory<JobListingFactory> */
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active_from' => 'immutable_datetime',
        'active_until' => 'immutable_datetime',
        'created_at' => 'immutable_datetime',
        'updated_at' => 'immutable_datetime',
        'employment_type' => EmploymentType::class,
        'workplace' => Workplace::class,
        'experience_level' => ExperienceLevel::class,
        'primary_canton_code' => SwissCanton::class,
        'primary_sub_region' => SwissSubRegion::class,
        'status' => JobStatus::class,
        'application_process' => ApplicationProcess::class,
        'salary_type' => SalaryType::class,
        'salary_option' => SalaryOption::class,
        'no_salary' => 'boolean',
        'workload_min' => 'integer',
        'workload_max' => 'integer',
        'experience_years_min' => 'integer',
        'experience_years_max' => 'integer',
        'primary_latitude' => 'float',
        'primary_longitude' => 'float',
        'has_multiple_locations' => 'boolean',
        'allows_remote' => 'boolean',
        'category' => JobCategory::class,
        'languages' => 'array',
        'application_documents' => 'array',
        'screening_questions' => 'array',
        'use_company_logo' => 'boolean',
        'use_company_banner' => 'boolean',
        'logo_dimensions' => 'array',
        'logo_uploaded_at' => 'datetime',
        'banner_dimensions' => 'array',
        'banner_uploaded_at' => 'datetime',
    ];

    /**
     * Get the company that owns the job listing.
     *
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the job tier associated with this job listing.
     *
     * @return BelongsTo<JobTier, $this>
     */
    public function jobTier(): BelongsTo
    {
        return $this->belongsTo(JobTier::class);
    }

    /**
     * Get the applications for this job listing.
     *
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the additional locations for this job listing.
     *
     * @return HasMany<JobListingAdditionalLocation, $this>
     */
    public function additionalLocations(): HasMany
    {
        return $this->hasMany(JobListingAdditionalLocation::class);
    }

    /**
     * Get the primary region based on the primary canton
     */
    public function primaryRegion(): ?SwissRegion
    {
        return $this->primary_canton_code?->region();
    }

    /**
     * Scope for jobs in a specific canton (checks both primary and additional locations)
     *
     * @param  Builder<JobListing>  $query
     * @return Builder<JobListing>
     */
    public function scopeInCanton(Builder $query, SwissCanton $canton): Builder
    {
        return $query->where(function ($query) use ($canton): void {
            $query->where('primary_canton_code', $canton->value)
                ->orWhereHas('additionalLocations', function ($query) use ($canton): void {
                    $query->where('canton_code', $canton->value);
                });
        });
    }

    /**
     * Scope for jobs in a specific region (checks both primary and additional locations)
     *
     * @param  Builder<JobListing>  $query
     * @return Builder<JobListing>
     */
    public function scopeInRegion(Builder $query, SwissRegion $region): Builder
    {
        $cantonCodes = $region->cantonCodes();

        return $query->where(function ($query) use ($cantonCodes): void {
            $query->whereIn('primary_canton_code', $cantonCodes)
                ->orWhereHas('additionalLocations', function ($query) use ($cantonCodes): void {
                    $query->whereIn('canton_code', $cantonCodes);
                });
        });
    }

    /**
     * Scope for remote jobs
     *
     * @param  Builder<JobListing>  $query
     * @return Builder<JobListing>
     */
    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('allows_remote', true);
    }

    /**
     * Scope for jobs in a specific sub-region
     *
     * @param  Builder<JobListing>  $query
     * @return Builder<JobListing>
     */
    public function scopeInSubRegion(Builder $query, SwissSubRegion $subRegion): Builder
    {
        return $query->where(function ($query) use ($subRegion): void {
            $query->where('primary_sub_region', $subRegion->value)
                ->orWhereHas('additionalLocations', function ($query) use ($subRegion): void {
                    $query->where('sub_region', $subRegion->value);
                });
        });
    }

    /**
     * Detect and set the sub-region based on postal code
     * If the postal code doesn't match any defined sub-region, it will leave the field null
     */
    public function detectAndSetSubRegion(): void
    {
        if (! empty($this->postcode)) {
            $this->primary_sub_region = SwissSubRegion::detectFromPostalCode((string) $this->postcode);
        }
    }

    /**
     * Get the skills for this job listing.
     *
     * @return BelongsToMany<Skill, static>
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class); // @phpstan-ignore-line
    }

    /**
     * Get the effective logo URL (custom or company fallback).
     */
    public function getEffectiveLogoUrlAttribute(): ?string
    {
        // If using custom logo and it exists, return custom logo URL
        if (! $this->use_company_logo && $this->logo_path && Storage::disk('public')->exists($this->logo_path)) {
            return Storage::disk('public')->url($this->logo_path);
        }

        // Fallback to company logo
        return $this->company->logo_url;
    }

    /**
     * Get the effective banner URL (custom or company fallback).
     */
    public function getEffectiveBannerUrlAttribute(): ?string
    {
        // If using custom banner and it exists, return custom banner URL
        if (! $this->use_company_banner && $this->banner_path && Storage::disk('public')->exists($this->banner_path)) {
            return Storage::disk('public')->url($this->banner_path);
        }

        // Fallback to company banner
        return $this->company->banner_url;
    }

    /**
     * Get the job listing logo URL (custom only).
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    /**
     * Get the job listing banner URL (custom only).
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (! $this->banner_path) {
            return null;
        }

        return Storage::disk('public')->url($this->banner_path);
    }

    /**
     * Check if the job listing has a custom logo uploaded.
     */
    public function hasCustomLogo(): bool
    {
        return ! is_null($this->logo_path) && Storage::disk('public')->exists($this->logo_path);
    }

    /**
     * Check if the job listing has a custom banner uploaded.
     */
    public function hasCustomBanner(): bool
    {
        return ! is_null($this->banner_path) && Storage::disk('public')->exists($this->banner_path);
    }

    /**
     * Delete the job listing custom logo file from storage.
     */
    public function deleteCustomLogo(): bool
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
                'use_company_logo' => true, // Reset to use company logo
            ]);
        }

        return $deleted;
    }

    /**
     * Delete the job listing custom banner file from storage.
     */
    public function deleteCustomBanner(): bool
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
                'use_company_banner' => true, // Reset to use company banner
            ]);
        }

        return $deleted;
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
