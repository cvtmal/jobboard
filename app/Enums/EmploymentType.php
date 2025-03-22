<?php

declare(strict_types=1);

namespace App\Enums;

enum EmploymentType: string
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';
    case FULL_PART_TIME = 'full-part-time';
    case CONTRACT = 'contract';
    case TEMPORARY = 'temporary';
    case INTERNSHIP = 'internship';
    case VOLUNTEER = 'volunteer';
    
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
     * Get the human-readable name of the employment type.
     */
    public function label(): string
    {
        return match($this) {
            self::FULL_TIME => 'Full time',
            self::PART_TIME => 'Part time',
            self::FULL_PART_TIME => 'Full/Part time',
            self::CONTRACT => 'Contract',
            self::TEMPORARY => 'Temporary',
            self::INTERNSHIP => 'Internship',
            self::VOLUNTEER => 'Volunteer',
        };
    }
}
