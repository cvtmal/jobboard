<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $job_listing_id
 * @property int $job_tier_id
 * @property Carbon $purchased_at
 * @property Carbon $expires_at
 * @property float $price_paid
 * @property float|null $discount_applied
 * @property string|null $promo_code
 * @property string $payment_status
 * @property string|null $payment_method
 * @property string|null $transaction_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read JobListing $jobListing
 * @property-read JobTier $jobTier
 */
final class JobListingSubscription extends Model
{
    /**
     * Payment status constants
     */
    public const string STATUS_PENDING = 'pending';

    public const string STATUS_COMPLETED = 'completed';

    public const string STATUS_FAILED = 'failed';

    public const string STATUS_REFUNDED = 'refunded';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'job_listing_id',
        'job_tier_id',
        'purchased_at',
        'expires_at',
        'price_paid',
        'discount_applied',
        'promo_code',
        'payment_status',
        'payment_method',
        'transaction_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'price_paid' => 'decimal:2',
        'discount_applied' => 'decimal:2',
    ];

    /**
     * Get the job listing associated with this subscription.
     *
     * @return BelongsTo<JobListing, $this>
     */
    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(JobListing::class);
    }

    /**
     * Get the job tier associated with this subscription.
     *
     * @return BelongsTo<JobTier, $this>
     */
    public function jobTier(): BelongsTo
    {
        return $this->belongsTo(JobTier::class);
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->payment_status === self::STATUS_COMPLETED
            && $this->expires_at->isFuture();
    }

    /**
     * Check if the subscription has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get the days remaining until expiration.
     */
    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int) $this->expires_at->diffInDays(now());
    }
}
