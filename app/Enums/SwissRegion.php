<?php

declare(strict_types=1);

namespace App\Enums;

enum SwissRegion: string
{
    case ZENTRALSCHWEIZ = 'central';
    case OSTSCHWEIZ = 'eastern';
    case ZURICH = 'zurich';
    case NORDWESTSCHWEIZ = 'northwest';
    case ESPACE_MITTELLAND = 'espace_mittelland';
    case REGION_LEMANIQUE = 'region_lemanique';
    case TICINO = 'ticino';

    public function label(): string
    {
        return match ($this) {
            self::ZENTRALSCHWEIZ => 'Zentralschweiz',
            self::OSTSCHWEIZ => 'Ostschweiz',
            self::ZURICH => 'Zürich',
            self::NORDWESTSCHWEIZ => 'Nordwestschweiz',
            self::ESPACE_MITTELLAND => 'Espace Mittelland',
            self::REGION_LEMANIQUE => 'Région lémanique',
            self::TICINO => 'Ticino',
        };
    }

    /**
     * Get all cantons in this region
     *
     * @return array<SwissCanton>
     */
    public function cantons(): array
    {
        return match ($this) {
            self::ZENTRALSCHWEIZ => [
                SwissCanton::LUCERNE, SwissCanton::URI, SwissCanton::SCHWYZ,
                SwissCanton::OBWALDEN, SwissCanton::NIDWALDEN, SwissCanton::ZUG,
            ],
            self::OSTSCHWEIZ => [
                SwissCanton::GLARUS, SwissCanton::SCHAFFHAUSEN, SwissCanton::APPENZELL_AUSSERRHODEN,
                SwissCanton::APPENZELL_INNERRHODEN, SwissCanton::ST_GALLEN, SwissCanton::GRAUBUNDEN,
                SwissCanton::THURGAU,
            ],
            self::ZURICH => [SwissCanton::ZURICH],
            self::NORDWESTSCHWEIZ => [
                SwissCanton::BASEL_STADT, SwissCanton::BASEL_LANDSCHAFT, SwissCanton::AARGAU,
            ],
            self::ESPACE_MITTELLAND => [
                SwissCanton::BERN, SwissCanton::FRIBOURG, SwissCanton::SOLOTHURN,
                SwissCanton::NEUCHATEL, SwissCanton::JURA,
            ],
            self::REGION_LEMANIQUE => [
                SwissCanton::VAUD, SwissCanton::VALAIS, SwissCanton::GENEVA,
            ],
            self::TICINO => [SwissCanton::TICINO],
        };
    }

    /**
     * Get canton codes for all cantons in this region
     *
     * @return array<string>
     */
    public function cantonCodes(): array
    {
        return array_map(fn (SwissCanton $canton) => $canton->value, $this->cantons());
    }
}
