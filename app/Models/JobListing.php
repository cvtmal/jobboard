<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\JobListingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $company_id
 * @property string|null $reference_number
 * @property string $title
 * @property string $description
 * @property string|null $employment_type
 * @property int|null $workload_min
 * @property int|null $workload_max
 * @property Carbon|null $active_from
 * @property Carbon|null $active_until
 * @property string|null $workplace
 * @property string|null $hierarchy
 * @property string|null $experience_level
 * @property int|null $experience_years_min
 * @property int|null $experience_years_max
 * @property string|null $education_level
 * @property string[]|null $languages
 * @property string|null $address
 * @property string|null $postcode
 * @property string|null $city
 * @property bool $no_salary
 * @property string|null $salary_type
 * @property string|null $salary_option
 * @property float|null $salary_min
 * @property float|null $salary_max
 * @property string|null $salary_currency
 * @property string|null $job_tier
 * @property string $application_process
 * @property string|null $application_email
 * @property string|null $application_url
 * @property string|null $contact_person
 * @property string|null $contact_email
 * @property string|null $internal_notes
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @property-read Collection<int, JobApplication> $applications
 * @property int|null $job_tier_id
 * @property-read int|null $applications_count
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
 *
 * @mixin Eloquent
 */
final class JobListing extends Model
{
    use HasFactory; // @phpstan-ignore-line

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active_from' => 'date',
        'active_until' => 'date',
        'workload_min' => 'integer',
        'workload_max' => 'integer',
        'experience_years_min' => 'integer',
        'experience_years_max' => 'integer',
        'languages' => 'array',
        'no_salary' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
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
     * Get the applications for this job listing.
     *
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
