import { screen, within } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import LiveTable from '@/Components/Live/LiveTable.vue';
import { liveGame, livePlayer } from '@/testing/live';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveGameData } from '@/types/live';

const fivePlayers = [
  livePlayer(10, 'Anna', false),
  livePlayer(11, 'Bernd'),
  livePlayer(12, 'Clara'),
  livePlayer(13, 'Dieter'),
  livePlayer(14, 'Eva'),
];

function game(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return liveGame({ dran: 12, vorhand: 12, ...overrides });
}

function seatNames(): string[] {
  return ['unten', 'links', 'oben', 'rechts'].map((position) => {
    const seat = screen.getByRole('group', { name: new RegExp(`, ${position}$`) });

    return seat.getAttribute('aria-label')!.split(',')[0];
  });
}

describe('LiveTable', () => {
  it('setzt mich unten und die Mitspieler links, oben und rechts', () => {
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
      onlinePlayerIds: [],
      liveGame: game(),
    });

    expect(seatNames()).toEqual(['Clara', 'Dieter', 'Eva', 'Bernd']);
  });

  it('dreht auch dann auf meinen Platz, wenn ich aussetze', () => {
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 10,
      onlinePlayerIds: [],
      liveGame: game(),
    });

    expect(seatNames()).toEqual(['Bernd', 'Clara', 'Dieter', 'Eva']);
    expect(screen.getByText('Du setzt dieses Spiel aus!')).toBeTruthy();
  });

  it('zeigt Zuschauern die Sitzreihenfolge ohne Drehung und ohne Aussetzen-Hinweis', () => {
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 999,
      onlinePlayerIds: [],
      liveGame: game(),
    });

    expect(seatNames()).toEqual(['Bernd', 'Clara', 'Dieter', 'Eva']);
    expect(screen.queryByText('Du setzt dieses Spiel aus!')).toBeNull();
  });

  it('markiert nur den Spieler, der dran ist', () => {
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
      onlinePlayerIds: [],
      liveGame: game({ dran: 13 }),
    });

    const marked = screen.getAllByText('dran');
    expect(marked).toHaveLength(1);
    expect(within(screen.getByRole('group', { name: 'Dieter, links' })).getByText('dran')).toBe(
      marked[0],
    );
  });

  it('zeigt Vorbehalt, Partei, Ansage und Spieltyp eines Spielers', () => {
    const state = game();
    state.spieler[13] = { ansage: 'Re', spieltyp: 'Hochzeit', partei: 'Re', vorbehalt: 'Hochzeit' };
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
      onlinePlayerIds: [],
      liveGame: state,
    });

    const seat = screen.getByRole('group', { name: 'Dieter, links' });
    expect(seat.textContent).toContain('Hochzeit');
    expect(seat.textContent).toContain('Re');
  });

  it('sagt „Vorhand“ nur in der Vorbehaltsphase', async () => {
    const { rerender } = renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
      onlinePlayerIds: [],
      liveGame: game({ phase: 2, vorhand: 13, dran: 13 }),
    });

    expect(
      within(screen.getByRole('group', { name: 'Dieter, links' })).getByText(/Vorhand/),
    ).toBeTruthy();

    await rerender({ liveGame: game({ phase: 4, vorhand: 13 }) });

    expect(screen.queryByText(/Vorhand/)).toBeNull();
  });

  it('zeigt vor dem ersten Spiel den Tisch ohne Markierung', () => {
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
      onlinePlayerIds: [],
      liveGame: null,
    });

    expect(seatNames()).toEqual(['Clara', 'Dieter', 'Eva', 'Bernd']);
    expect(screen.queryByText('dran')).toBeNull();
    expect(screen.queryByText('Du setzt dieses Spiel aus!')).toBeNull();
  });
});
