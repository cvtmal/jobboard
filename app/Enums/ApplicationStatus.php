<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case REVIEWING = 'reviewing';
    case SHORTLISTED = 'shortlisted';
    case INTERVIEWING = 'interviewing';
    case OFFERED = 'offered';
    case HIRED = 'hired';
    case REJECTED = 'rejected';

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
     * Get the human-readable name of the application status.
     */
    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::PENDING => 'Pending',
            self::REVIEWING => 'Reviewing',
            self::SHORTLISTED => 'Shortlisted',
            self::INTERVIEWING => 'Interviewing',
            self::OFFERED => 'Offered',
            self::HIRED => 'Hired',
            self::REJECTED => 'Rejected',
        };
    }
}
