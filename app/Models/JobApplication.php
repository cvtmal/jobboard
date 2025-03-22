<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\JobApplication
 *
 * @property int $id
 * @property int $job_listing_id
 * @property int $applicant_id
 * @property string $cv_path
 * @property string|null $cover_letter_path
 * @property string|null $additional_documents_path
 * @property ApplicationStatus $status
 * @property \Carbon\Carbon $applied_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Applicant $applicant
 * @property-read JobListing $jobListing
 */
final class JobApplication extends Model
{
    /** @use HasFactory<\Database\Factories\JobApplicationFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ApplicationStatus::class,
        'applied_at' => 'datetime',
    ];

    /**
     * Get the job listing that this application belongs to.
     *
     * @return BelongsTo<JobListing, $this>
     */
    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    /**
     * Get the applicant that this application belongs to.
     *
     * @return BelongsTo<Applicant, $this>
     */
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
