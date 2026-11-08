import { CARD_RATIO, type TableSize } from '@/Components/Live/liveHandLayout';

/** Plätze am Tisch aus Sicht des Betrachters; `unten` bin ich. */
export type TrickSeat = 'unten' | 'links' | 'oben' | 'rechts';

/** Kartenhöhe im Stich im Verhältnis zur Tischhöhe. */
export const TRICK_CARD_HEIGHT = 0.26;

export type TrickLayout = {
  cardWidth: number;
  cardHeight: number;
  /** Maße des Kreuzes aus allen vier Plätzen. */
  width: number;
  height: number;
  /** Obere linke Ecke jedes Platzes, gemessen von der oberen linken Ecke des Kreuzes. */
  offsets: Record<TrickSeat, { x: number; y: number }>;
};

/**
 * Der Stich liegt über Kreuz: Jede Karte vor ihrem Spieler, links und rechts um 0,8 Kartenbreiten
 * versetzt, oben und unten um 0,36 Kartenhöhen. Alle Karten stehen aufrecht, damit die Eckzeichen
 * lesbar bleiben. Die Zahlen stammen aus dem Prototyp „Kartenprobe“ (Spec #36, Vorgaben aus #94).
 */
export function trickLayout(table: TableSize): TrickLayout {
  const cardHeight = table.height * TRICK_CARD_HEIGHT;
  const cardWidth = cardHeight / CARD_RATIO;
  const sideShift = cardWidth * 0.8;
  const verticalShift = cardHeight * 0.36;

  return {
    cardWidth,
    cardHeight,
    width: cardWidth + 2 * sideShift,
    height: cardHeight + 2 * verticalShift,
    offsets: {
      links: { x: 0, y: verticalShift },
      oben: { x: sideShift, y: 0 },
      rechts: { x: 2 * sideShift, y: verticalShift },
      unten: { x: sideShift, y: 2 * verticalShift },
    },
  };
}
