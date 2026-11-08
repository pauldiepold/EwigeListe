import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import PlayingCard from '@/Components/Live/PlayingCard.vue';
import { cardFaceUrl } from '@/Components/Live/playingCards';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveCard, LiveCardRank, LiveCardSuit } from '@/types/live';

const kreuzDame: LiveCard = { farbe: 4, wert: 3 };

describe('PlayingCard', () => {
  it.each([
    [{ farbe: 4, wert: 3 }, 'Kreuz Dame'],
    [{ farbe: 3, wert: 2 }, 'Pik Bube'],
    [{ farbe: 2, wert: 5 }, 'Herz 10'],
    [{ farbe: 1, wert: 4 }, 'Karo König'],
    [{ farbe: 4, wert: 6 }, 'Kreuz Ass'],
    [{ farbe: 2, wert: 1 }, 'Herz 9'],
  ] as [LiveCard, string][])('nennt die Karte %j „%s“, Farbe vor Rang', (card, name) => {
    renderWithUi(PlayingCard, { card });

    expect(screen.getByRole('img', { name })).toBeTruthy();
  });

  it('hat für alle 24 Karten ein Kartenbild', () => {
    const suits: LiveCardSuit[] = [1, 2, 3, 4];
    const ranks: LiveCardRank[] = [1, 2, 3, 4, 5, 6];

    for (const farbe of suits) {
      for (const wert of ranks) {
        expect(cardFaceUrl({ farbe, wert })).toMatch(/.svg/);
      }
    }
  });

  it('ist ohne Interaktion kein Button', () => {
    renderWithUi(PlayingCard, { card: kreuzDame });

    expect(screen.queryByRole('button')).toBeNull();
  });

  it('meldet das Antippen einer spielbaren Karte', async () => {
    const onPress = vi.fn();
    const user = userEvent.setup();
    renderWithUi(PlayingCard, { card: kreuzDame, interactive: true, onPress });

    await user.click(screen.getByRole('button', { name: 'Kreuz Dame' }));

    expect(onPress).toHaveBeenCalledWith(kreuzDame);
  });

  it('sperrt eine nicht spielbare Karte', async () => {
    const onPress = vi.fn();
    const user = userEvent.setup();
    renderWithUi(PlayingCard, { card: kreuzDame, interactive: true, playable: false, onPress });

    const button = screen.getByRole('button', { name: 'Kreuz Dame' }) as HTMLButtonElement;
    await user.click(button);

    expect(button.disabled).toBe(true);
    expect(onPress).not.toHaveBeenCalled();
  });

  it('meldet die Auswahl für die Armut über aria-pressed', async () => {
    const { rerender } = renderWithUi(PlayingCard, {
      card: kreuzDame,
      interactive: true,
      selected: false,
    });
    const button = screen.getByRole('button', { name: 'Kreuz Dame' });
    expect(button.getAttribute('aria-pressed')).toBe('false');

    await rerender({ card: kreuzDame, interactive: true, selected: true });

    expect(button.getAttribute('aria-pressed')).toBe('true');
  });

  it('setzt aria-pressed nur, wenn die Karte auswählbar ist', () => {
    renderWithUi(PlayingCard, { card: kreuzDame, interactive: true });

    expect(screen.getByRole('button').hasAttribute('aria-pressed')).toBe(false);
  });

  it('zeigt ohne Karte nur einen leeren Platz', () => {
    renderWithUi(PlayingCard, { card: null });

    expect(screen.queryByRole('button')).toBeNull();
    expect(screen.queryByRole('img')).toBeNull();
  });

  describe('erhaltene Armut-Karte', () => {
    it('nennt eine erhaltene Karte auch in ihrem Namen', () => {
      renderWithUi(PlayingCard, { card: kreuzDame, received: true });

      expect(screen.getByRole('img', { name: 'Kreuz Dame (aus der Armut erhalten)' })).toBeTruthy();
    });

    it('nennt eine normale Karte nur bei ihrem Namen', () => {
      renderWithUi(PlayingCard, { card: kreuzDame });

      expect(screen.getByRole('img', { name: 'Kreuz Dame' })).toBeTruthy();
    });

    it('bleibt als erhaltene Karte ein Button', () => {
      renderWithUi(PlayingCard, { card: kreuzDame, interactive: true, received: true });

      expect(
        screen.getByRole('button', { name: 'Kreuz Dame (aus der Armut erhalten)' }),
      ).toBeTruthy();
    });
  });
});
