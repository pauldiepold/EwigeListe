import { screen, within } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import LiveTable from '@/Components/Live/LiveTable.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveGameData, LivePlayer } from '@/types/live';

function player(seat: number, name: string, isActive = true): LivePlayer {
  return {
    id: 10 + seat,
    firstName: name,
    lastName: 'Test',
    fullName: `${name} Test`,
    avatarPath: null,
    isActive,
  };
}

const fivePlayers = [
  player(0, 'Anna', false),
  player(1, 'Bernd'),
  player(2, 'Clara'),
  player(3, 'Dieter'),
  player(4, 'Eva'),
];

function game(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return {
    id: 1,
    phase: 4,
    dran: 12,
    stichNr: 1,
    vorhand: 12,
    aktuellerStich: { karten: [], stecher: null },
    letzterStich: { karten: [], stecher: null },
    spieler: {
      11: { ansage: '', spieltyp: '', partei: '', vorbehalt: '' },
      12: { ansage: '', spieltyp: '', partei: '', vorbehalt: '' },
      13: { ansage: '', spieltyp: '', partei: '', vorbehalt: '' },
      14: { ansage: '', spieltyp: '', partei: '', vorbehalt: '' },
    },
    messages: [],
    spieltyp: '',
    schweinchen: false,
    kontrasOffengelegt: 0,
    reAnsage: false,
    kontraAnsage: false,
    reAbsage: null,
    kontraAbsage: null,
    ...overrides,
  };
}

function seatNames(): string[] {
  return ['unten', 'links', 'oben', 'rechts'].map((position) => {
    const seat = screen.getByRole('group', { name: new RegExp(`, ${position}$`) });

    return seat.getAttribute('aria-label')!.split(',')[0];
  });
}

describe('LiveTable', () => {
  it('setzt mich unten und die Mitspieler links, oben und rechts', () => {
    renderWithUi(LiveTable, { players: fivePlayers, viewerPlayerId: 12, liveGame: game() });

    expect(seatNames()).toEqual(['Clara', 'Dieter', 'Eva', 'Bernd']);
  });

  it('dreht auch dann auf meinen Platz, wenn ich aussetze', () => {
    renderWithUi(LiveTable, { players: fivePlayers, viewerPlayerId: 10, liveGame: game() });

    expect(seatNames()).toEqual(['Bernd', 'Clara', 'Dieter', 'Eva']);
    expect(screen.getByText('Du setzt dieses Spiel aus!')).toBeTruthy();
  });

  it('zeigt Zuschauern die Sitzreihenfolge ohne Drehung und ohne Aussetzen-Hinweis', () => {
    renderWithUi(LiveTable, { players: fivePlayers, viewerPlayerId: 999, liveGame: game() });

    expect(seatNames()).toEqual(['Bernd', 'Clara', 'Dieter', 'Eva']);
    expect(screen.queryByText('Du setzt dieses Spiel aus!')).toBeNull();
  });

  it('markiert nur den Spieler, der dran ist', () => {
    renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
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
    renderWithUi(LiveTable, { players: fivePlayers, viewerPlayerId: 12, liveGame: state });

    const seat = screen.getByRole('group', { name: 'Dieter, links' });
    expect(seat.textContent).toContain('Hochzeit');
    expect(seat.textContent).toContain('Re');
  });

  it('sagt „Vorhand“ nur in der Vorbehaltsphase', async () => {
    const { rerender } = renderWithUi(LiveTable, {
      players: fivePlayers,
      viewerPlayerId: 12,
      liveGame: game({ phase: 2, vorhand: 13, dran: 13 }),
    });

    expect(
      within(screen.getByRole('group', { name: 'Dieter, links' })).getByText(/Vorhand/),
    ).toBeTruthy();

    await rerender({ liveGame: game({ phase: 4, vorhand: 13 }) });

    expect(screen.queryByText(/Vorhand/)).toBeNull();
  });

  it('zeigt vor dem ersten Spiel den Tisch ohne Markierung', () => {
    renderWithUi(LiveTable, { players: fivePlayers, viewerPlayerId: 12, liveGame: null });

    expect(seatNames()).toEqual(['Clara', 'Dieter', 'Eva', 'Bernd']);
    expect(screen.queryByText('dran')).toBeNull();
    expect(screen.queryByText('Du setzt dieses Spiel aus!')).toBeNull();
  });
});
