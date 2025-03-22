<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationProcess;
use App\Enums\EmploymentType;
use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobTier;
use App\Enums\SalaryOption;
use App\Enums\SalaryType;
use App\Enums\Workplace;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Job extends Model
{
    use HasFactory; // @phpstan-ignore-line

    /**
     * The table associated with the model.
     */
    protected $table = 'job_listings';

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
        'job_tier' => JobTier::class,
        'salary_currency' => 'string',
    ];

    /**
     * Get the company that owns the job.
     *
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
