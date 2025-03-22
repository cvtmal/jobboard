<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use Carbon\CarbonImmutable;
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
 * @property JobStatus $status
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property int|null $job_tier_id
 * @property-read Collection<int, JobApplication> $applications
 * @property-read int|null $applications_count
 * @property-read Company $company
 * @property-read JobTier|null $jobTier
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
        'languages' => 'array',
        'workload_min' => 'integer',
        'workload_max' => 'integer',
        'experience_years_min' => 'integer',
        'experience_years_max' => 'integer',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'no_salary' => 'boolean',
        'employment_type' => EmploymentType::class,
        'workplace' => Workplace::class,
        'experience_level' => ExperienceLevel::class,
        'salary_type' => SalaryType::class,
        'salary_option' => SalaryOption::class,
        'application_process' => ApplicationProcess::class,
        'status' => JobStatus::class,
        'salary_currency' => 'string',
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
}
