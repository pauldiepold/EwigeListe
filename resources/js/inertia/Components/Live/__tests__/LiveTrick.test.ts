import { screen } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import LiveTrick from '@/Components/Live/LiveTrick.vue';
import { trickLayout } from '@/Components/Live/liveTrickLayout';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveCardRank, LiveCardSuit, LiveEngineCard, LivePlayer } from '@/types/live';

function player(id: number, name: string, isActive = true): LivePlayer {
  return {
    id,
    firstName: name,
    lastName: 'Test',
    fullName: `${name} Test`,
    avatarPath: null,
    isActive,
  };
}

/** Anna setzt aus; Clara ist der Betrachter. */
const players = [
  player(10, 'Anna', false),
  player(11, 'Bernd'),
  player(12, 'Clara'),
  player(13, 'Dieter'),
  player(14, 'Eva'),
];

function played(
  id: number,
  farbe: LiveCardSuit,
  wert: LiveCardRank,
  gespieltVon: number,
): LiveEngineCard {
  return {
    id,
    farbe,
    wert,
    rang: 0,
    punkte: 0,
    trumpf: false,
    spielbar: false,
    gespieltVon,
    armut_zurueck: false,
  };
}

const tableSize = { width: 800, height: 400 };

function renderTrick(props: Record<string, unknown> = {}) {
  return renderWithUi(LiveTrick, {
    cards: [],
    players,
    viewerPlayerId: 12,
    tableSize,
    ...props,
  });
}

const slot = (position: string) =>
  screen
    .getByRole('group', { name: 'Stich' })
    .querySelector<HTMLElement>(`[data-trick-slot="${position}"]`)!;

describe('trickLayout', () => {
  it('macht die Karte 26 % so hoch wie den Tisch und versetzt die Plätze über Kreuz', () => {
    const layout = trickLayout({ width: 800, height: 500 });

    expect(layout.cardHeight).toBeCloseTo(130);
    expect(layout.cardWidth).toBeCloseTo(130 / 1.4);
    expect(layout.offsets.links.x).toBe(0);
    expect(layout.offsets.oben.x).toBeCloseTo(layout.cardWidth * 0.8);
    expect(layout.offsets.rechts.x).toBeCloseTo(layout.cardWidth * 1.6);
    expect(layout.offsets.oben.y).toBe(0);
    expect(layout.offsets.links.y).toBeCloseTo(130 * 0.36);
    expect(layout.offsets.unten.y).toBeCloseTo(130 * 0.72);
    expect(layout.offsets.unten.x).toBeCloseTo(layout.offsets.oben.x);
  });
});

describe('LiveTrick', () => {
  it('legt die Karten vor die Spieler, aus Sicht des Betrachters', () => {
    renderTrick({
      cards: [played(1, 3, 4, 13), played(2, 1, 2, 12), played(3, 4, 3, 14), played(4, 2, 5, 11)],
    });

    const name = (position: string) =>
      slot(position).querySelector('[role="img"]')?.getAttribute('aria-label');
    expect(name('unten')).toBe('Karo Bube');
    expect(name('links')).toBe('Pik König');
    expect(name('oben')).toBe('Kreuz Dame');
    expect(name('rechts')).toBe('Herz 10');
  });

  it('schichtet die Karten in der Reihenfolge, in der sie gelegt wurden', () => {
    renderTrick({ cards: [played(1, 3, 4, 13), played(2, 1, 2, 12)] });

    expect(Number(slot('links').style.zIndex)).toBeLessThan(Number(slot('unten').style.zIndex));
  });

  it('zeigt freie Plätze als gestrichelten Umriss, den Hilfsmittel überspringen', () => {
    renderTrick({ cards: [played(1, 3, 4, 13)] });

    const empty = slot('oben').firstElementChild!;
    expect(empty.getAttribute('aria-hidden')).toBe('true');
    expect(empty.className).toContain('border-dashed');
    expect(screen.getAllByRole('img')).toHaveLength(1);
  });

  it('dreht keine Karte, damit die Eckzeichen lesbar bleiben', () => {
    renderTrick({ cards: [played(1, 3, 4, 13), played(2, 1, 2, 11)] });

    for (const position of ['links', 'rechts', 'oben', 'unten']) {
      expect(slot(position).style.transform).not.toContain('rotate');
    }
  });

  it('hebt nur die Karte des Gewinners hervor', () => {
    renderTrick({
      cards: [played(1, 3, 4, 13), played(2, 1, 2, 12)],
      winnerId: 13,
    });

    expect(slot('links').dataset.winner).toBeDefined();
    expect(slot('unten').dataset.winner).toBeUndefined();
  });

  it('zeigt Zuschauern die Sitzreihenfolge ohne Drehung', () => {
    renderTrick({ viewerPlayerId: 10, cards: [played(1, 3, 4, 11)] });

    expect(slot('unten').querySelector('[role="img"]')?.getAttribute('aria-label')).toBe(
      'Pik König',
    );
  });
});
