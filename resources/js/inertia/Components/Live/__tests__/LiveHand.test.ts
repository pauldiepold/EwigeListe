import { screen, within } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import LiveHand from '@/Components/Live/LiveHand.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveCardRank, LiveCardSuit, LiveEngineCard } from '@/types/live';

function card(id: number, farbe: LiveCardSuit, wert: LiveCardRank): LiveEngineCard {
  return {
    id,
    farbe,
    wert,
    rang: 0,
    punkte: 0,
    trumpf: false,
    spielbar: true,
    gespieltVon: false,
    armut_zurueck: false,
  };
}

const kreuzDame = card(1, 4, 3);
const herzZehn = card(2, 2, 5);
const pikBube = card(3, 3, 2);
const hand = [kreuzDame, herzZehn, pikBube];

const tableSize = { width: 800, height: 400 };

function renderHand(props: Record<string, unknown> = {}) {
  return renderWithUi(LiveHand, { cards: hand, tableSize, ...props });
}

describe('LiveHand', () => {
  it('zeigt die Karten in der Reihenfolge, die die Engine liefert', () => {
    renderHand();

    const names = within(screen.getByRole('group', { name: 'Deine Hand' }))
      .getAllByRole('img')
      .map((image) => image.getAttribute('aria-label'));
    expect(names).toEqual(['Kreuz Dame', 'Herz 10', 'Pik Bube']);
  });

  it('zeigt eine leere Hand ohne Karten', () => {
    renderHand({ cards: [] });

    expect(screen.queryByRole('img')).toBeNull();
  });

  it('fächert die Karten auf, links gekippt nach links und rechts nach rechts', () => {
    renderHand();

    const wrappers = screen
      .getAllByRole('img')
      .map((image) => image.closest<HTMLElement>('[data-hand-card]')!);
    const angleOf = (element: HTMLElement) =>
      Number(/rotate\((-?[\d.]+)deg\)/.exec(element.style.transform)![1]);
    expect(angleOf(wrappers[0])).toBeLessThan(0);
    expect(angleOf(wrappers[1])).toBeCloseTo(0);
    expect(angleOf(wrappers[2])).toBeGreaterThan(0);
  });

  it('legt die Karten in die Reihenfolge der Hand übereinander, rechts liegt oben', () => {
    renderHand();

    const zIndexes = screen
      .getAllByRole('img')
      .map((image) => Number(image.closest<HTMLElement>('[data-hand-card]')!.style.zIndex));
    expect(zIndexes).toEqual([1, 2, 3]);
  });

  it('ist ohne Interaktion nur ein Bild', () => {
    renderHand();

    expect(screen.queryByRole('button')).toBeNull();
  });

  describe('interaktiv', () => {
    it('meldet die gedrückte Karte', async () => {
      const onPress = vi.fn();
      renderHand({ interactive: true, onPress });

      await userEvent.click(screen.getByRole('button', { name: 'Herz 10' }));

      expect(onPress).toHaveBeenCalledWith(herzZehn);
    });

    it('dunkelt nicht spielbare Karten ab und sperrt sie', async () => {
      const onPress = vi.fn();
      renderHand({
        interactive: true,
        isPlayable: (c: LiveEngineCard) => c.id !== herzZehn.id,
        onPress,
      });

      const locked = screen.getByRole('button', { name: 'Herz 10' }) as HTMLButtonElement;
      expect(locked.disabled).toBe(true);
      expect(
        (screen.getByRole('button', { name: 'Kreuz Dame' }) as HTMLButtonElement).disabled,
      ).toBe(false);
      await userEvent.click(locked);
      expect(onPress).not.toHaveBeenCalled();
    });

    it('kennzeichnet die erhaltenen Armut-Karten', () => {
      renderHand({ interactive: true, receivedIds: [herzZehn.id] });

      expect(
        screen.getByRole('button', { name: 'Herz 10 (aus der Armut erhalten)' }),
      ).toBeTruthy();
      expect(screen.getByRole('button', { name: 'Kreuz Dame' })).toBeTruthy();
      expect(screen.getByRole('button', { name: 'Pik Bube' })).toBeTruthy();
    });

    it('macht die Karten zu Schaltern, sobald es eine Auswahl gibt', () => {
      renderHand({ interactive: true, selectedIds: [pikBube.id] });

      expect(screen.getByRole('button', { name: 'Pik Bube' }).getAttribute('aria-pressed')).toBe(
        'true',
      );
      expect(screen.getByRole('button', { name: 'Kreuz Dame' }).getAttribute('aria-pressed')).toBe(
        'false',
      );
    });
  });
});
