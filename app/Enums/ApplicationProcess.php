<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationProcess: string
{
    case EMAIL = 'email';
    case URL = 'url';
    case BOTH = 'both';
    
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
     * Get the human-readable name of the application process.
     */
    public function label(): string
    {
        return match($this) {
            self::EMAIL => 'Email',
            self::URL => 'URL',
            self::BOTH => 'Email & URL',
        };
    }
}
