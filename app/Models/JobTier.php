<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\JobTierFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $duration_days
 * @property bool $featured
 * @property int|null $max_applications
 * @property int $max_active_jobs
 * @property bool $has_analytics
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, JobListing> $jobs
 * @property-read int|null $jobs_count
 *
 * @method static JobTierFactory factory($count = null, $state = [])
 * @method static Builder<static>|JobTier newModelQuery()
 * @method static Builder<static>|JobTier newQuery()
 * @method static Builder<static>|JobTier query()
 * @method static Builder<static>|JobTier whereCreatedAt($value)
 * @method static Builder<static>|JobTier whereDescription($value)
 * @method static Builder<static>|JobTier whereDurationDays($value)
 * @method static Builder<static>|JobTier whereFeatured($value)
 * @method static Builder<static>|JobTier whereHasAnalytics($value)
 * @method static Builder<static>|JobTier whereId($value)
 * @method static Builder<static>|JobTier whereMaxActiveJobs($value)
 * @method static Builder<static>|JobTier whereMaxApplications($value)
 * @method static Builder<static>|JobTier whereName($value)
 * @method static Builder<static>|JobTier wherePrice($value)
 * @method static Builder<static>|JobTier whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class JobTier extends Model
{
    /** @use HasFactory<JobTierFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'featured' => 'boolean',
        'has_analytics' => 'boolean',
        'max_applications' => 'integer',
        'max_active_jobs' => 'integer',
        'duration_days' => 'integer',
    ];

    /**
     * Get the jobs associated with this job tier.
     *
     * @return HasMany<JobListing, $this>
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(JobListing::class);
    }
}
