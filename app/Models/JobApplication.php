<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use Carbon\Carbon;
use Database\Factories\JobApplicationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
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
 * @property Carbon $applied_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Applicant $applicant
 * @property-read JobListing $jobListing
 *
 * @method static JobApplicationFactory factory($count = null, $state = [])
 * @method static Builder<static>|JobApplication newModelQuery()
 * @method static Builder<static>|JobApplication newQuery()
 * @method static Builder<static>|JobApplication query()
 * @method static Builder<static>|JobApplication whereAdditionalDocumentsPath($value)
 * @method static Builder<static>|JobApplication whereApplicantId($value)
 * @method static Builder<static>|JobApplication whereAppliedAt($value)
 * @method static Builder<static>|JobApplication whereCoverLetterPath($value)
 * @method static Builder<static>|JobApplication whereCreatedAt($value)
 * @method static Builder<static>|JobApplication whereCvPath($value)
 * @method static Builder<static>|JobApplication whereId($value)
 * @method static Builder<static>|JobApplication whereJobListingId($value)
 * @method static Builder<static>|JobApplication whereStatus($value)
 * @method static Builder<static>|JobApplication whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
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
