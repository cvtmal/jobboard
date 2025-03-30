<?php

declare(strict_types=1);

namespace App\Enums;

enum SwissCanton: string
{
    case ZURICH = 'ZH';
    case BERN = 'BE';
    case LUCERNE = 'LU';
    case URI = 'UR';
    case SCHWYZ = 'SZ';
    case OBWALDEN = 'OW';
    case NIDWALDEN = 'NW';
    case GLARUS = 'GL';
    case ZUG = 'ZG';
    case FRIBOURG = 'FR';
    case SOLOTHURN = 'SO';
    case BASEL_STADT = 'BS';
    case BASEL_LANDSCHAFT = 'BL';
    case SCHAFFHAUSEN = 'SH';
    case APPENZELL_AUSSERRHODEN = 'AR';
    case APPENZELL_INNERRHODEN = 'AI';
    case ST_GALLEN = 'SG';
    case GRAUBUNDEN = 'GR';
    case AARGAU = 'AG';
    case THURGAU = 'TG';
    case TICINO = 'TI';
    case VAUD = 'VD';
    case VALAIS = 'VS';
    case NEUCHATEL = 'NE';
    case GENEVA = 'GE';
    case JURA = 'JU';
    case LIECHTENSTEIN = 'FL';

    public function label(): string
    {
        return match ($this) {
            self::ZURICH => 'Zürich',
            self::BERN => 'Bern',
            self::LUCERNE => 'Luzern',
            self::URI => 'Uri',
            self::SCHWYZ => 'Schwyz',
            self::OBWALDEN => 'Obwalden',
            self::NIDWALDEN => 'Nidwalden',
            self::GLARUS => 'Glarus',
            self::ZUG => 'Zug',
            self::FRIBOURG => 'Fribourg',
            self::SOLOTHURN => 'Solothurn',
            self::BASEL_STADT => 'Basel-Stadt',
            self::BASEL_LANDSCHAFT => 'Basel-Landschaft',
            self::SCHAFFHAUSEN => 'Schaffhausen',
            self::APPENZELL_AUSSERRHODEN => 'Appenzell Ausserrhoden',
            self::APPENZELL_INNERRHODEN => 'Appenzell Innerrhoden',
            self::ST_GALLEN => 'St. Gallen',
            self::GRAUBUNDEN => 'Graubünden',
            self::AARGAU => 'Aargau',
            self::THURGAU => 'Thurgau',
            self::TICINO => 'Ticino',
            self::VAUD => 'Vaud',
            self::VALAIS => 'Valais',
            self::NEUCHATEL => 'Neuchâtel',
            self::GENEVA => 'Genève',
            self::JURA => 'Jura',
            self::LIECHTENSTEIN => 'Fürstentum Liechtenstein',
        };
    }

    public function region(): SwissRegion
    {
        return match ($this) {
            self::ZURICH => SwissRegion::ZURICH,
            self::BERN, self::FRIBOURG, self::SOLOTHURN, self::NEUCHATEL, self::JURA => SwissRegion::ESPACE_MITTELLAND,
            self::LUCERNE, self::URI, self::SCHWYZ, self::OBWALDEN, self::NIDWALDEN, self::ZUG => SwissRegion::ZENTRALSCHWEIZ,
            self::GLARUS, self::SCHAFFHAUSEN, self::APPENZELL_AUSSERRHODEN, self::APPENZELL_INNERRHODEN, self::ST_GALLEN, self::GRAUBUNDEN, self::THURGAU, self::LIECHTENSTEIN => SwissRegion::OSTSCHWEIZ,
            self::BASEL_STADT, self::BASEL_LANDSCHAFT, self::AARGAU => SwissRegion::NORDWESTSCHWEIZ,
            self::VAUD, self::VALAIS, self::GENEVA => SwissRegion::REGION_LEMANIQUE,
            self::TICINO => SwissRegion::TICINO,
        };
    }
}
