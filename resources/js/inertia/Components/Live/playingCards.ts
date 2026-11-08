import type { LiveCard, LiveCardRank, LiveCardSuit } from '@/types/live';

type SuitInfo = { code: string; name: string; red: boolean; path: string };

/**
 * Farbsymbole aus Adrian Kennards CC0-Kartensatz (siehe `assets/cards/LICENSE.md`), gezeichnet
 * auf einer Fläche von −600 … 600. Schlüssel ist die Farbe der Live-Engine.
 */
const SUITS: Record<LiveCardSuit, SuitInfo> = {
  1: {
    code: 'D',
    name: 'Karo',
    red: true,
    path: 'M-400 0C-350 0 0 -450 0 -500C0 -450 350 0 400 0C350 0 0 450 0 500C0 450 -350 0 -400 0Z',
  },
  2: {
    code: 'H',
    name: 'Herz',
    red: true,
    path: 'M0 -300C0 -400 100 -500 200 -500C300 -500 400 -400 400 -250C400 0 0 400 0 500C0 400 -400 0 -400 -250C-400 -400 -300 -500 -200 -500C-100 -500 0 -400 0 -300Z',
  },
  3: {
    code: 'S',
    name: 'Pik',
    red: false,
    path: 'M0 -500C100 -250 355 -100 355 185A150 150 0 0 1 55 185A10 10 0 0 0 35 185C35 385 85 400 130 500L-130 500C-85 400 -35 385 -35 185A10 10 0 0 0 -55 185A150 150 0 0 1 -355 185C-355 -100 -100 -250 0 -500Z',
  },
  4: {
    code: 'C',
    name: 'Kreuz',
    red: false,
    path: 'M30 150C35 385 85 400 130 500L-130 500C-85 400 -35 385 -30 150A10 10 0 0 0 -50 150A210 210 0 1 1 -124 -51A10 10 0 0 0 -110 -65A230 230 0 1 1 110 -65A10 10 0 0 0 124 -51A210 210 0 1 1 50 150A10 10 0 0 0 30 150Z',
  },
};

/** Rang der Live-Engine, Dateiname im Kartensatz, Name und Zeichen im Eckzeichen. */
const RANKS: Record<LiveCardRank, { code: string; name: string; index: string }> = {
  1: { code: '9', name: '9', index: '9' },
  2: { code: 'J', name: 'Bube', index: 'B' },
  3: { code: 'Q', name: 'Dame', index: 'D' },
  4: { code: 'K', name: 'König', index: 'K' },
  5: { code: '10', name: '10', index: '10' },
  6: { code: 'A', name: 'Ass', index: 'A' },
};

const FACES_DIR = '../../assets/cards';

const faceUrls = import.meta.glob<string>('../../assets/cards/*.svg', {
  eager: true,
  query: '?url',
  import: 'default',
});

/** Zugänglicher Name mit Farbe vor Rang, z. B. „Kreuz Dame“. */
export function cardName(card: LiveCard): string {
  return `${SUITS[card.farbe].name} ${RANKS[card.wert].name}`;
}

/** Zeichen im Eckzeichen: 9, 10, B, D, K oder A. */
export function cardIndex(card: LiveCard): string {
  return RANKS[card.wert].index;
}

/** Herz und Karo sind rot, Kreuz und Pik schwarz. */
export function isRedCard(card: LiveCard): boolean {
  return SUITS[card.farbe].red;
}

/** Umriss des Farbsymbols für ein `viewBox` von −600 … 600. */
export function cardSuitPath(card: LiveCard): string {
  return SUITS[card.farbe].path;
}

/** URL des Kartenbilds (Figur oder Augenbild), von Vite mit Hash versehen. */
export function cardFaceUrl(card: LiveCard): string {
  const file = `${FACES_DIR}/${RANKS[card.wert].code}${SUITS[card.farbe].code}.svg`;
  const url = faceUrls[file];
  if (!url) {
    throw new Error(`Kartenbild fehlt: ${file}`);
  }

  return url;
}
