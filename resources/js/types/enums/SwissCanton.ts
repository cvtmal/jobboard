import { SwissRegion } from './SwissRegion';

export enum SwissCanton {
    ZURICH = 'ZH',
    BERN = 'BE',
    LUCERNE = 'LU',
    URI = 'UR',
    SCHWYZ = 'SZ',
    OBWALDEN = 'OW',
    NIDWALDEN = 'NW',
    GLARUS = 'GL',
    ZUG = 'ZG',
    FRIBOURG = 'FR',
    SOLOTHURN = 'SO',
    BASEL_STADT = 'BS',
    BASEL_LANDSCHAFT = 'BL',
    SCHAFFHAUSEN = 'SH',
    APPENZELL_AUSSERRHODEN = 'AR',
    APPENZELL_INNERRHODEN = 'AI',
    ST_GALLEN = 'SG',
    GRAUBUNDEN = 'GR',
    AARGAU = 'AG',
    THURGAU = 'TG',
    TICINO = 'TI',
    VAUD = 'VD',
    VALAIS = 'VS',
    NEUCHATEL = 'NE',
    GENEVA = 'GE',
    JURA = 'JU',
    LIECHTENSTEIN = 'FL',
}

export interface SwissCantonMetadata {
    id: SwissCanton;
    label: string;
    region: SwissRegion;
}

export const swissCantonData: Record<SwissCanton, SwissCantonMetadata> = {
    [SwissCanton.ZURICH]: {
        id: SwissCanton.ZURICH,
        label: 'Zürich',
        region: SwissRegion.ZURICH,
    },
    [SwissCanton.BERN]: {
        id: SwissCanton.BERN,
        label: 'Bern',
        region: SwissRegion.ESPACE_MITTELLAND,
    },
    [SwissCanton.LUCERNE]: {
        id: SwissCanton.LUCERNE,
        label: 'Luzern',
        region: SwissRegion.ZENTRALSCHWEIZ,
    },
    [SwissCanton.URI]: {
        id: SwissCanton.URI,
        label: 'Uri',
        region: SwissRegion.ZENTRALSCHWEIZ,
    },
    [SwissCanton.SCHWYZ]: {
        id: SwissCanton.SCHWYZ,
        label: 'Schwyz',
        region: SwissRegion.ZENTRALSCHWEIZ,
    },
    [SwissCanton.OBWALDEN]: {
        id: SwissCanton.OBWALDEN,
        label: 'Obwalden',
        region: SwissRegion.ZENTRALSCHWEIZ,
    },
    [SwissCanton.NIDWALDEN]: {
        id: SwissCanton.NIDWALDEN,
        label: 'Nidwalden',
        region: SwissRegion.ZENTRALSCHWEIZ,
    },
    [SwissCanton.GLARUS]: {
        id: SwissCanton.GLARUS,
        label: 'Glarus',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.ZUG]: {
        id: SwissCanton.ZUG,
        label: 'Zug',
        region: SwissRegion.ZENTRALSCHWEIZ,
    },
    [SwissCanton.FRIBOURG]: {
        id: SwissCanton.FRIBOURG,
        label: 'Freiburg',
        region: SwissRegion.ESPACE_MITTELLAND,
    },
    [SwissCanton.SOLOTHURN]: {
        id: SwissCanton.SOLOTHURN,
        label: 'Solothurn',
        region: SwissRegion.ESPACE_MITTELLAND,
    },
    [SwissCanton.BASEL_STADT]: {
        id: SwissCanton.BASEL_STADT,
        label: 'Basel-Stadt',
        region: SwissRegion.NORDWESTSCHWEIZ,
    },
    [SwissCanton.BASEL_LANDSCHAFT]: {
        id: SwissCanton.BASEL_LANDSCHAFT,
        label: 'Basel-Landschaft',
        region: SwissRegion.NORDWESTSCHWEIZ,
    },
    [SwissCanton.SCHAFFHAUSEN]: {
        id: SwissCanton.SCHAFFHAUSEN,
        label: 'Schaffhausen',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.APPENZELL_AUSSERRHODEN]: {
        id: SwissCanton.APPENZELL_AUSSERRHODEN,
        label: 'Appenzell Ausserrhoden',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.APPENZELL_INNERRHODEN]: {
        id: SwissCanton.APPENZELL_INNERRHODEN,
        label: 'Appenzell Innerrhoden',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.ST_GALLEN]: {
        id: SwissCanton.ST_GALLEN,
        label: 'St. Gallen',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.GRAUBUNDEN]: {
        id: SwissCanton.GRAUBUNDEN,
        label: 'Graubünden',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.AARGAU]: {
        id: SwissCanton.AARGAU,
        label: 'Aargau',
        region: SwissRegion.NORDWESTSCHWEIZ,
    },
    [SwissCanton.THURGAU]: {
        id: SwissCanton.THURGAU,
        label: 'Thurgau',
        region: SwissRegion.OSTSCHWEIZ,
    },
    [SwissCanton.TICINO]: {
        id: SwissCanton.TICINO,
        label: 'Tessin',
        region: SwissRegion.TICINO,
    },
    [SwissCanton.VAUD]: {
        id: SwissCanton.VAUD,
        label: 'Waadt',
        region: SwissRegion.REGION_LEMANIQUE,
    },
    [SwissCanton.VALAIS]: {
        id: SwissCanton.VALAIS,
        label: 'Wallis',
        region: SwissRegion.REGION_LEMANIQUE,
    },
    [SwissCanton.NEUCHATEL]: {
        id: SwissCanton.NEUCHATEL,
        label: 'Neuenburg',
        region: SwissRegion.ESPACE_MITTELLAND,
    },
    [SwissCanton.GENEVA]: {
        id: SwissCanton.GENEVA,
        label: 'Genf',
        region: SwissRegion.REGION_LEMANIQUE,
    },
    [SwissCanton.JURA]: {
        id: SwissCanton.JURA,
        label: 'Jura',
        region: SwissRegion.ESPACE_MITTELLAND,
    },
    [SwissCanton.LIECHTENSTEIN]: {
        id: SwissCanton.LIECHTENSTEIN,
        label: 'Fürstentum Liechtenstein',
        region: SwissRegion.OSTSCHWEIZ,
    },
};
