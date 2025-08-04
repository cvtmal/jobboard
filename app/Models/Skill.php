<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\SkillFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property bool $active
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, JobListing> $jobListings
 * @property-read int|null $job_listings_count
 *
 * @method static SkillFactory factory($count = null, $state = [])
 * @method static Builder<static>|Skill newModelQuery()
 * @method static Builder<static>|Skill newQuery()
 * @method static Builder<static>|Skill query()
 * @method static Builder<static>|Skill whereId($value)
 * @method static Builder<static>|Skill whereName($value)
 * @method static Builder<static>|Skill whereSlug($value)
 * @method static Builder<static>|Skill whereDescription($value)
 * @method static Builder<static>|Skill whereActive($value)
 * @method static Builder<static>|Skill whereCreatedAt($value)
 * @method static Builder<static>|Skill whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
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
        'active' => 'boolean',
    ];

    /**
     * Get the job listings that have this skill.
     *
     * @return BelongsToMany<JobListing, static>
     */
    public function jobListings(): BelongsToMany
    {
        return $this->belongsToMany(JobListing::class); // @phpstan-ignore-line
    }
}
