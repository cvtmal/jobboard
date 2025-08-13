<?php

declare(strict_types=1);

namespace App\Enums;

enum ExperienceLevel: string
{
    case ENTRY = 'entry';
    case JUNIOR = 'junior';
    case MID_LEVEL = 'mid-level';
    case PROFESSIONAL = 'professional';
    case SENIOR = 'senior';
    case EXECUTIVE = 'executive';

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
     * Get the human-readable name of the experience level.
     */
    public function label(): string
    {
        return match ($this) {
            self::ENTRY => 'Entry Level',
            self::JUNIOR => 'Junior',
            self::MID_LEVEL => 'Mid-Level',
            self::PROFESSIONAL => 'Professional',
            self::SENIOR => 'Senior',
            self::EXECUTIVE => 'Executive',
        };
    }
}
