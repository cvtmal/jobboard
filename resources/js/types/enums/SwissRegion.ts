export enum SwissRegion {
  ZURICH = 'zurich',
  ESPACE_MITTELLAND = 'espace_mittelland',
  NORDWESTSCHWEIZ = 'nordwestschweiz',
  OSTSCHWEIZ = 'ostschweiz',
  ZENTRALSCHWEIZ = 'central',
  REGION_LEMANIQUE = 'lemanique',
  TICINO = 'ticino',
}

export interface SwissRegionMetadata {
  id: SwissRegion;
  label: string;
  cantons: Array<string>;
}

export const swissRegionData: Record<SwissRegion, SwissRegionMetadata> = {
  [SwissRegion.ZURICH]: {
    id: SwissRegion.ZURICH,
    label: 'Zürich',
    cantons: ['ZH'],
  },
  [SwissRegion.ESPACE_MITTELLAND]: {
    id: SwissRegion.ESPACE_MITTELLAND,
    label: 'Espace Mittelland',
    cantons: ['BE', 'SO', 'FR', 'NE', 'JU'],
  },
  [SwissRegion.NORDWESTSCHWEIZ]: {
    id: SwissRegion.NORDWESTSCHWEIZ,
    label: 'Nordwestschweiz',
    cantons: ['BS', 'BL', 'AG'],
  },
  [SwissRegion.OSTSCHWEIZ]: {
    id: SwissRegion.OSTSCHWEIZ,
    label: 'Ostschweiz',
    cantons: ['GL', 'SH', 'AR', 'AI', 'SG', 'GR', 'TG', 'FL'],
  },
  [SwissRegion.ZENTRALSCHWEIZ]: {
    id: SwissRegion.ZENTRALSCHWEIZ,
    label: 'Zentralschweiz',
    cantons: ['LU', 'UR', 'SZ', 'OW', 'NW', 'ZG'],
  },
  [SwissRegion.REGION_LEMANIQUE]: {
    id: SwissRegion.REGION_LEMANIQUE,
    label: 'Région lémanique',
    cantons: ['GE', 'VD', 'VS'],
  },
  [SwissRegion.TICINO]: {
    id: SwissRegion.TICINO,
    label: 'Ticino',
    cantons: ['TI'],
  },
};
