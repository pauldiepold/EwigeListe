import { describe, expect, it } from 'vitest';
import { handLayout } from '@/Components/Live/liveHandLayout';

const TABLE = { width: 800, height: 400 };

describe('handLayout', () => {
  it('macht die Karten 36 % der Tischhöhe hoch, im Seitenverhältnis 5 : 7', () => {
    const layout = handLayout(12, TABLE);

    expect(layout.cardHeight).toBeCloseTo(144);
    expect(layout.cardWidth).toBeCloseTo(144 / 1.4);
  });

  it('dreht die Karten um einen Punkt 12 Kartenhöhen unter ihrer Oberkante', () => {
    expect(handLayout(12, TABLE).radius).toBeCloseTo(12 * 144);
  });

  it('fächert die Karten symmetrisch auf, die linke Karte kippt nach links', () => {
    const { angles } = handLayout(11, TABLE);

    expect(angles).toHaveLength(11);
    expect(angles[5]).toBeCloseTo(0);
    expect(angles[0]).toBeLessThan(0);
    expect(angles[0]).toBeCloseTo(-angles[10]);
    expect([...angles].sort((a, b) => a - b)).toEqual(angles);
  });

  it('wählt den Winkel so, dass der Kartenabstand am Rand des Bogens dem Abstand entspricht', () => {
    // 12 Karten füllen 78 % der Tischbreite: Abstand (0,78 × 800 − 102,86) / 11 ≈ 47,38 px,
    // Winkel im Bogenmaß ≈ Abstand / Radius = 47,38 / 1728.
    const { angles } = handLayout(12, TABLE);

    const stepInRadians = ((angles[1] - angles[0]) * Math.PI) / 180;
    expect(stepInRadians).toBeCloseTo(47.38 / 1728, 4);
  });

  it('rückt wenige Karten höchstens um 0,72 Kartenbreiten auseinander', () => {
    const { angles, cardWidth, radius } = handLayout(3, TABLE);

    const stepInRadians = ((angles[1] - angles[0]) * Math.PI) / 180;
    expect(stepInRadians * radius).toBeCloseTo(0.72 * cardWidth, 0);
  });

  it('stellt eine einzelne Karte gerade in die Mitte', () => {
    expect(handLayout(1, TABLE).angles).toEqual([0]);
  });

  it('lässt die Hand unten rund ein Drittel aus dem Tisch ragen, solange sie nicht gefächert ist', () => {
    const { top, cardHeight } = handLayout(1, TABLE);

    expect(top + cardHeight).toBeCloseTo(TABLE.height + cardHeight / 3);
  });

  it('rückt die Hand auf schmalen Tischen nach rechts, auf breiten steht sie mittig', () => {
    expect(handLayout(12, TABLE).centerX).toBeCloseTo(0.56 * 800);
    expect(handLayout(12, { width: 1200, height: 500 }).centerX).toBeCloseTo(600);
  });

  it('hebt einen breiten Fächer an, damit die äußeren Karten ihr Eckzeichen zeigen', () => {
    expect(handLayout(12, TABLE).top).toBeLessThan(handLayout(1, TABLE).top);
  });

  it('liefert für eine leere Hand keine Karten und für einen Tisch ohne Größe keine NaN', () => {
    expect(handLayout(0, TABLE).angles).toEqual([]);
    const flat = handLayout(12, { width: 0, height: 0 });
    expect([flat.top, flat.cardWidth, flat.radius, ...flat.angles].every(Number.isFinite)).toBe(
      true,
    );
  });
});
