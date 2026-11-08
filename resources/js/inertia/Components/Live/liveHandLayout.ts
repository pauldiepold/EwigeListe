/** Seitenverhältnis einer Karte: Höhe geteilt durch Breite (250 × 350). */
export const CARD_RATIO = 1.4;

export type TableSize = { width: number; height: number };

export type HandLayout = {
  cardWidth: number;
  cardHeight: number;
  /** Waagerechte Mitte des Fächers, gemessen vom linken Tischrand. */
  centerX: number;
  /** Abstand der Kartenoberkante vom oberen Tischrand. */
  top: number;
  /** Abstand zwischen Kartenoberkante und Drehpunkt des Fächers. */
  radius: number;
  /** Drehung je Karte in Grad, von links nach rechts. */
  angles: number[];
};

/** Kartenhöhe der Hand im Verhältnis zur Tischhöhe. */
export const HAND_CARD_HEIGHT = 0.36;

/** Unter dieser Tischbreite (Smartphone quer) rückt die Hand nach rechts. */
export const NARROW_TABLE_WIDTH = 1024;

/**
 * Die Hand als leichter Fächer: Alle Karten drehen sich um einen gemeinsamen Punkt weit unter dem
 * Tisch (Radius 12 Kartenhöhen). Der Abstand der Karten ist `min((0,78 × Tischbreite − Kartenbreite)
 * / (n − 1), 0,72 × Kartenbreite)`. Die Hand ragt unten etwa 40 % aus dem Tisch
 * (lesbar bleiben muss nur das Eckzeichen oben links; so verdecken Overlays in der Mitte es nicht)
 * und wird um die Durchbiegung des Bogens angehoben, damit auch die äußeren Karten ihr Eckzeichen
 * zeigen. Auf schmalen Tischen sitzt die Hand bei 56 % der Breite statt mittig, damit mein Badge
 * unten links die linken Karten nicht verdeckt; rechts unten ist Platz.
 *
 * Die Zahlen stammen aus dem Prototyp „Kartenprobe“ (Spec #36, Vorgaben aus #94); Fächer und
 * Überstand nach dem ersten Spieltest flacher bzw. tiefer.
 */
export function handLayout(count: number, table: TableSize): HandLayout {
  const cardHeight = table.height * HAND_CARD_HEIGHT;
  const cardWidth = cardHeight / CARD_RATIO;
  const radius = cardHeight * 12;

  const step =
    count > 1
      ? Math.max(0, Math.min((table.width * 0.78 - cardWidth) / (count - 1), cardWidth * 0.72))
      : 0;
  const angleStep = 2 * Math.asin(step / (2 * radius || 1));

  const maxAngle = (angleStep * (count - 1)) / 2;
  const dip = radius * (1 - Math.cos(maxAngle)) + (cardWidth / 2) * Math.sin(maxAngle);

  return {
    cardWidth,
    cardHeight,
    centerX: table.width * (table.width < NARROW_TABLE_WIDTH ? 0.56 : 0.5),
    top: table.height - cardHeight * 0.6 - dip * 0.6,
    radius,
    angles: Array.from(
      { length: Math.max(count, 0) },
      (_, index) => ((index - (count - 1) / 2) * angleStep * 180) / Math.PI,
    ),
  };
}
