<?php

declare(strict_types=1);

namespace App\Enums;

enum JobTier: string
{
    case BASIC = 'basic';
    case PREMIUM = 'premium';
    case ENTERPRISE = 'enterprise';
    
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
     * Get the human-readable name of the job tier.
     */
    public function label(): string
    {
        return match($this) {
            self::BASIC => 'Basic',
            self::PREMIUM => 'Premium',
            self::ENTERPRISE => 'Enterprise',
        };
    }
}
