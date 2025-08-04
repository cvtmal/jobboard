<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SwissCanton;
use App\Enums\SwissRegion;
use App\Enums\SwissSubRegion;
use Database\Factories\JobListingAdditionalLocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class JobListingAdditionalLocation extends Model
{
    /** @use HasFactory<JobListingAdditionalLocationFactory> */
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'canton_code' => SwissCanton::class,
        'sub_region' => SwissSubRegion::class,
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Get the job listing that owns the additional location
     *
     * @return BelongsTo<JobListing, $this>
     */
    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    /**
     * Get the region based on the canton
     */
    public function region(): ?SwissRegion
    {
        return $this->canton_code?->region();
    }

    /**
     * Detect and set the sub-region based on postal code
     * If the postal code doesn't match any defined sub-region, it will leave the field null
     */
    public function detectAndSetSubRegion(): void
    {
        if (! empty($this->postcode)) {
            $this->sub_region = SwissSubRegion::detectFromPostalCode((string) $this->postcode);
        }
    }
}
