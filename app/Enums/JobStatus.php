<?php

declare(strict_types=1);

namespace App\Enums;

enum JobStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case EXPIRED = 'expired';
    case CLOSED = 'closed';
    
    /**
     * Get all available values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    
    /**
     * Get the human-readable name of the job status.
     */
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::PUBLISHED => 'Published',
            self::EXPIRED => 'Expired',
            self::CLOSED => 'Closed',
        };
    }
}
