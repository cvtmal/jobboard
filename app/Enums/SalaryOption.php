<?php

declare(strict_types=1);

namespace App\Enums;

enum SalaryOption: string
{
    case FIXED = 'fixed';
    case RANGE = 'range';
    case NEGOTIABLE = 'negotiable';

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
     * Get the human-readable name of the salary option.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed',
            self::RANGE => 'Range',
            self::NEGOTIABLE => 'Negotiable',
        };
    }
}
