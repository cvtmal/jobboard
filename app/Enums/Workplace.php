<?php

declare(strict_types=1);

namespace App\Enums;

enum Workplace: string
{
    case REMOTE = 'remote';
    case ONSITE = 'onsite';
    case HYBRID = 'hybrid';
    
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
     * Get the human-readable name of the workplace type.
     */
    public function label(): string
    {
        return match($this) {
            self::REMOTE => 'Remote',
            self::ONSITE => 'Onsite',
            self::HYBRID => 'Hybrid',
        };
    }
}
