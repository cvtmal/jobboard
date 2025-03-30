import { SwissCanton } from './SwissCanton';
import { SwissRegion } from './SwissRegion';

export enum SwissSubRegion {
  ZURICH_CITY = 'zurich_city',
  ZURICH_OBERLAND = 'zurich_oberland',
  ZURICH_UNTERLAND = 'zurich_unterland',
  WINTERTHUR = 'winterthur',
  SCHAFFHAUSEN = 'schaffhausen',
  WIL_TOGGENBURG = 'wil_toggenburg',
  THURGAU_BODENSEE = 'thurgau_bodensee',
  ST_GALLEN_APPENZELL = 'st_gallen_appenzell',
  RHEINTAL_FL_SARGANS_LINTH = 'rheintal_fl_sargans_linth',
  GRAUBUNDEN = 'graubunden',
  AARGAU_SOLOTHURN = 'aargau_solothurn',
  BASEL = 'basel',
  GENF = 'genf',
  NEUCHATEL_JURA = 'neuchatel_jura',
  FRIBOURG = 'fribourg',
  WAADT_UNTERWALLIS = 'waadt_unterwallis',
  TESSIN = 'tessin',
  ZENTRALSCHWEIZ = 'zentralschweiz',
  BERN = 'bern',
}

export interface SwissSubRegionMetadata {
  id: SwissSubRegion;
  label: string;
  region: SwissRegion;
  cantons: Array<SwissCanton>;
}

export const swissSubRegionData: Record<SwissSubRegion, SwissSubRegionMetadata> = {
  [SwissSubRegion.ZURICH_CITY]: {
    id: SwissSubRegion.ZURICH_CITY,
    label: 'Stadt Zürich',
    region: SwissRegion.ZURICH,
    cantons: [SwissCanton.ZURICH],
  },
  [SwissSubRegion.ZURICH_OBERLAND]: {
    id: SwissSubRegion.ZURICH_OBERLAND,
    label: 'Zürcher Oberland',
    region: SwissRegion.ZURICH,
    cantons: [SwissCanton.ZURICH],
  },
  [SwissSubRegion.ZURICH_UNTERLAND]: {
    id: SwissSubRegion.ZURICH_UNTERLAND,
    label: 'Zürcher Unterland',
    region: SwissRegion.ZURICH,
    cantons: [SwissCanton.ZURICH],
  },
  [SwissSubRegion.WINTERTHUR]: {
    id: SwissSubRegion.WINTERTHUR,
    label: 'Winterthur',
    region: SwissRegion.ZURICH,
    cantons: [SwissCanton.ZURICH],
  },
  [SwissSubRegion.SCHAFFHAUSEN]: {
    id: SwissSubRegion.SCHAFFHAUSEN,
    label: 'Schaffhausen',
    region: SwissRegion.OSTSCHWEIZ,
    cantons: [SwissCanton.SCHAFFHAUSEN],
  },
  [SwissSubRegion.WIL_TOGGENBURG]: {
    id: SwissSubRegion.WIL_TOGGENBURG,
    label: 'Wil/Toggenburg',
    region: SwissRegion.OSTSCHWEIZ,
    cantons: [SwissCanton.ST_GALLEN],
  },
  [SwissSubRegion.THURGAU_BODENSEE]: {
    id: SwissSubRegion.THURGAU_BODENSEE,
    label: 'Thurgau/Bodensee',
    region: SwissRegion.OSTSCHWEIZ,
    cantons: [SwissCanton.THURGAU],
  },
  [SwissSubRegion.ST_GALLEN_APPENZELL]: {
    id: SwissSubRegion.ST_GALLEN_APPENZELL,
    label: 'St. Gallen/Appenzell',
    region: SwissRegion.OSTSCHWEIZ,
    cantons: [SwissCanton.ST_GALLEN, SwissCanton.APPENZELL_AUSSERRHODEN, SwissCanton.APPENZELL_INNERRHODEN],
  },
  [SwissSubRegion.RHEINTAL_FL_SARGANS_LINTH]: {
    id: SwissSubRegion.RHEINTAL_FL_SARGANS_LINTH,
    label: 'Rheintal/FL/Sargans/Linth',
    region: SwissRegion.OSTSCHWEIZ,
    cantons: [SwissCanton.ST_GALLEN, SwissCanton.GLARUS, SwissCanton.LIECHTENSTEIN],
  },
  [SwissSubRegion.GRAUBUNDEN]: {
    id: SwissSubRegion.GRAUBUNDEN,
    label: 'Graubünden',
    region: SwissRegion.OSTSCHWEIZ,
    cantons: [SwissCanton.GRAUBUNDEN],
  },
  [SwissSubRegion.AARGAU_SOLOTHURN]: {
    id: SwissSubRegion.AARGAU_SOLOTHURN,
    label: 'Aargau/Solothurn',
    region: SwissRegion.NORDWESTSCHWEIZ,
    cantons: [SwissCanton.AARGAU, SwissCanton.SOLOTHURN],
  },
  [SwissSubRegion.BASEL]: {
    id: SwissSubRegion.BASEL,
    label: 'Basel',
    region: SwissRegion.NORDWESTSCHWEIZ,
    cantons: [SwissCanton.BASEL_STADT, SwissCanton.BASEL_LANDSCHAFT],
  },
  [SwissSubRegion.GENF]: {
    id: SwissSubRegion.GENF,
    label: 'Genf',
    region: SwissRegion.REGION_LEMANIQUE,
    cantons: [SwissCanton.GENEVA],
  },
  [SwissSubRegion.NEUCHATEL_JURA]: {
    id: SwissSubRegion.NEUCHATEL_JURA,
    label: 'Neuchatel/Jura',
    region: SwissRegion.ESPACE_MITTELLAND,
    cantons: [SwissCanton.NEUCHATEL, SwissCanton.JURA],
  },
  [SwissSubRegion.FRIBOURG]: {
    id: SwissSubRegion.FRIBOURG,
    label: 'Fribourg',
    region: SwissRegion.ESPACE_MITTELLAND,
    cantons: [SwissCanton.FRIBOURG],
  },
  [SwissSubRegion.WAADT_UNTERWALLIS]: {
    id: SwissSubRegion.WAADT_UNTERWALLIS,
    label: 'Waadt/Unterwallis',
    region: SwissRegion.REGION_LEMANIQUE,
    cantons: [SwissCanton.VAUD, SwissCanton.VALAIS],
  },
  [SwissSubRegion.TESSIN]: {
    id: SwissSubRegion.TESSIN,
    label: 'Tessin',
    region: SwissRegion.TICINO,
    cantons: [SwissCanton.TICINO],
  },
  [SwissSubRegion.ZENTRALSCHWEIZ]: {
    id: SwissSubRegion.ZENTRALSCHWEIZ,
    label: 'Zentralschweiz',
    region: SwissRegion.ZENTRALSCHWEIZ,
    cantons: [SwissCanton.LUCERNE, SwissCanton.URI, SwissCanton.SCHWYZ, SwissCanton.OBWALDEN, SwissCanton.NIDWALDEN, SwissCanton.ZUG],
  },
  [SwissSubRegion.BERN]: {
    id: SwissSubRegion.BERN,
    label: 'Bern',
    region: SwissRegion.ESPACE_MITTELLAND,
    cantons: [SwissCanton.BERN],
  },
};
