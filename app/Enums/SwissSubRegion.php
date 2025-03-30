<?php

declare(strict_types=1);

namespace App\Enums;

enum SwissSubRegion: string
{
    case ZURICH_CITY = 'zurich_city';
    case ZURICH_OBERLAND = 'zurich_oberland';
    case ZURICH_UNTERLAND = 'zurich_unterland';
    case WINTERTHUR = 'winterthur';

    case SCHAFFHAUSEN = 'schaffhausen';
    case WIL_TOGGENBURG = 'wil_toggenburg';
    case THURGAU_BODENSEE = 'thurgau_bodensee';
    case ST_GALLEN_APPENZELL = 'st_gallen_appenzell';
    case RHEINTAL_FL_SARGANS_LINTH = 'rheintal_fl_sargans_linth';
    case GRAUBUNDEN = 'graubunden';

    case AARGAU_SOLOTHURN = 'aargau_solothurn';

    case BASEL = 'basel';

    case GENF = 'genf';
    case NEUCHATEL_JURA = 'neuchatel_jura';
    case FRIBOURG = 'fribourg';
    case WAADT_UNTERWALLIS = 'waadt_unterwallis';

    case TESSIN = 'tessin';

    case ZENTRALSCHWEIZ = 'zentralschweiz';

    case BERN = 'bern';

    /**
     * Get all sub-regions for a specific region
     *
     * @return array<self>
     */
    public static function forRegion(SwissRegion $region): array
    {
        return match ($region) {
            SwissRegion::ZURICH => [
                self::ZURICH_CITY,
                self::ZURICH_OBERLAND,
                self::ZURICH_UNTERLAND,
                self::WINTERTHUR,
            ],
            SwissRegion::OSTSCHWEIZ => [
                self::SCHAFFHAUSEN,
                self::WIL_TOGGENBURG,
                self::THURGAU_BODENSEE,
                self::ST_GALLEN_APPENZELL,
                self::RHEINTAL_FL_SARGANS_LINTH,
                self::GRAUBUNDEN,
            ],
            SwissRegion::NORDWESTSCHWEIZ => [
                self::AARGAU_SOLOTHURN, // Note: partially spans two regions
                self::BASEL,
            ],
            SwissRegion::ESPACE_MITTELLAND => [
                self::NEUCHATEL_JURA,
                self::FRIBOURG,
                self::BERN,
            ],
            SwissRegion::REGION_LEMANIQUE => [
                self::GENF,
                self::WAADT_UNTERWALLIS,
            ],
            SwissRegion::TICINO => [
                self::TESSIN,
            ],
            SwissRegion::ZENTRALSCHWEIZ => [
                self::ZENTRALSCHWEIZ,
            ],
        };
    }

    /**
     * Get all sub-regions for a specific canton
     *
     * @return array<self>
     */
    public static function forCanton(SwissCanton $canton): array
    {
        return match ($canton) {
            SwissCanton::ZURICH => [self::ZURICH_CITY, self::ZURICH_OBERLAND, self::ZURICH_UNTERLAND, self::WINTERTHUR],
            SwissCanton::SCHAFFHAUSEN => [self::SCHAFFHAUSEN],
            SwissCanton::THURGAU => [self::THURGAU_BODENSEE],
            SwissCanton::ST_GALLEN => [self::ST_GALLEN_APPENZELL, self::WIL_TOGGENBURG, self::RHEINTAL_FL_SARGANS_LINTH],
            SwissCanton::APPENZELL_AUSSERRHODEN, SwissCanton::APPENZELL_INNERRHODEN => [self::ST_GALLEN_APPENZELL],
            SwissCanton::GLARUS => [self::RHEINTAL_FL_SARGANS_LINTH],
            SwissCanton::GRAUBUNDEN => [self::GRAUBUNDEN],
            SwissCanton::LIECHTENSTEIN => [self::RHEINTAL_FL_SARGANS_LINTH],
            SwissCanton::AARGAU => [self::AARGAU_SOLOTHURN],
            SwissCanton::SOLOTHURN => [self::AARGAU_SOLOTHURN],
            SwissCanton::BASEL_STADT, SwissCanton::BASEL_LANDSCHAFT => [self::BASEL],
            SwissCanton::GENEVA => [self::GENF],
            SwissCanton::NEUCHATEL, SwissCanton::JURA => [self::NEUCHATEL_JURA],
            SwissCanton::FRIBOURG => [self::FRIBOURG],
            SwissCanton::VAUD, SwissCanton::VALAIS => [self::WAADT_UNTERWALLIS],
            SwissCanton::TICINO => [self::TESSIN],
            SwissCanton::BERN => [self::BERN],
            SwissCanton::LUCERNE, SwissCanton::URI, SwissCanton::SCHWYZ,
            SwissCanton::OBWALDEN, SwissCanton::NIDWALDEN, SwissCanton::ZUG => [self::ZENTRALSCHWEIZ],
        };
    }

    /**
     * Detect sub-region from a postal code
     * Currently implemented only for Zurich region with good accuracy
     * Other regions need detailed postal code mappings
     */
    public static function detectFromPostalCode(string $postalCode): ?self
    {
        foreach (self::cases() as $subRegion) {
            if (in_array($postalCode, $subRegion->postalCodes(), true)) {
                return $subRegion;
            }
        }

        return null;
    }

    public function label(): string
    {
        return match ($this) {
            self::ZURICH_CITY => 'Stadt Zürich',
            self::ZURICH_OBERLAND => 'Zürcher Oberland',
            self::ZURICH_UNTERLAND => 'Zürcher Unterland',
            self::WINTERTHUR => 'Winterthur',

            self::SCHAFFHAUSEN => 'Schaffhausen',
            self::WIL_TOGGENBURG => 'Wil/Toggenburg',
            self::THURGAU_BODENSEE => 'Thurgau/Bodensee',
            self::ST_GALLEN_APPENZELL => 'St. Gallen/Appenzell',
            self::RHEINTAL_FL_SARGANS_LINTH => 'Rheintal/FL/Sargans/Linth',
            self::GRAUBUNDEN => 'Graubünden',

            self::AARGAU_SOLOTHURN => 'Aargau/Solothurn',

            self::BASEL => 'Basel',

            self::GENF => 'Genf',
            self::NEUCHATEL_JURA => 'Neuchatel/Jura',
            self::FRIBOURG => 'Fribourg',
            self::WAADT_UNTERWALLIS => 'Waadt/Unterwallis',

            self::TESSIN => 'Tessin',

            self::ZENTRALSCHWEIZ => 'Zentralschweiz',
            self::BERN => 'Bern',
        };
    }

    public function region(): SwissRegion
    {
        return match ($this) {
            self::ZURICH_CITY, self::ZURICH_OBERLAND, self::ZURICH_UNTERLAND, self::WINTERTHUR => SwissRegion::ZURICH,

            self::SCHAFFHAUSEN, self::WIL_TOGGENBURG, self::THURGAU_BODENSEE, self::ST_GALLEN_APPENZELL,
            self::RHEINTAL_FL_SARGANS_LINTH, self::GRAUBUNDEN => SwissRegion::OSTSCHWEIZ,

            self::AARGAU_SOLOTHURN => SwissRegion::NORDWESTSCHWEIZ, // primarily NW Switzerland

            self::BASEL => SwissRegion::NORDWESTSCHWEIZ,

            self::GENF => SwissRegion::REGION_LEMANIQUE,
            self::NEUCHATEL_JURA => SwissRegion::ESPACE_MITTELLAND,
            self::FRIBOURG => SwissRegion::ESPACE_MITTELLAND,
            self::WAADT_UNTERWALLIS => SwissRegion::REGION_LEMANIQUE,

            self::TESSIN => SwissRegion::TICINO,

            self::ZENTRALSCHWEIZ => SwissRegion::ZENTRALSCHWEIZ,
            self::BERN => SwissRegion::ESPACE_MITTELLAND,
        };
    }

    /**
     * Get postal codes representing this sub-region (sample/common ones)
     *
     * @return array<string>
     */
    public function postalCodes(): array
    {
        return match ($this) {
            self::ZURICH_CITY => ['8000', '8001', '8002', '8003', '8004', '8005', '8006', '8008', '8037', '8038', '8041', '8044', '8045', '8046', '8047', '8048', '8049', '8050', '8051', '8052', '8053', '8055', '8057'],
            self::ZURICH_OBERLAND => ['8132', '8133', '8134', '8135', '8344', '8345', '8607', '8610', '8614', '8615', '8617', '8618', '8620', '8625', '8626', '8627', '8635', '8636', '8637'],
            self::ZURICH_UNTERLAND => ['8302', '8303', '8304', '8305', '8306', '8307', '8309', '8310', '8311', '8315', '8317', '8320', '8322', '8424', '8425', '8426', '8427'],
            self::WINTERTHUR => ['8400', '8401', '8402', '8403', '8404', '8405', '8406', '8408', '8409', '8412', '8413', '8414', '8415'],
            // Other regions (samples only)
            default => [],
        };
    }
}
