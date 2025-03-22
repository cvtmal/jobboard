<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\JobTierFactory;
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
     * @return HasMany<Job, $this>
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
