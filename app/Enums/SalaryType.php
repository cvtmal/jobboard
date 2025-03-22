<?php

declare(strict_types=1);

namespace App\Enums;

enum SalaryType: string
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

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
     * Get the human-readable name of the salary type.
     */
    public function label(): string
    {
        return match ($this) {
            self::HOURLY => 'Hourly',
            self::DAILY => 'Daily',
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }
}
