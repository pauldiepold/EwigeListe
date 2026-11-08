import { screen, within } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import LiveScoring from '@/Components/Live/LiveScoring.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveGameData, LivePlayer } from '@/types/live';

function player(id: number, firstName: string): LivePlayer {
  return {
    id,
    firstName,
    lastName: 'Test',
    fullName: `${firstName} Test`,
    avatarPath: null,
    isActive: true,
  };
}

const players = [
  player(10, 'Anna'),
  player(11, 'Bernd'),
  player(12, 'Clara'),
  player(13, 'Dieter'),
];

function display() {
  return { ansage: '', spieltyp: '', partei: '', vorbehalt: '' };
}

/** Bernd und Clara (Re) gewinnen mit drei Wertungspunkten; Dieter spielt auf der Seite der Verlierer. */
function endedGame(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return {
    id: 4,
    phase: 101,
    dran: 11,
    stichNr: 13,
    vorhand: 11,
    aktuellerStich: { karten: [], stecher: null },
    letzterStich: { karten: [], stecher: 11 },
    spieler: { 11: display(), 12: display(), 13: display(), 14: display() },
    messages: [],
    spieltyp: '',
    schweinchen: false,
    kontrasOffengelegt: 0,
    reAnsage: false,
    kontraAnsage: false,
    reAbsage: null,
    kontraAbsage: null,
    winners: [11, 12],
    wertung: [
      ['Gewonnen', '+1'],
      ['Gegen die Alten', '+1'],
      ['Fuchs gefangen', '+1'],
    ],
    reAugen: 134,
    kontraAugen: 106,
    gewinntRe: true,
    wertungsPunkte: 3,
    ...overrides,
  };
}

function renderScoring(viewerPlayerId: number, game: LiveGameData = endedGame()) {
  return renderWithUi(LiveScoring, { game, players, viewerPlayerId });
}

describe('LiveScoring', () => {
  it('sagt mir, dass ich gewonnen habe, wenn ich zu den Gewinnern gehöre', () => {
    renderScoring(11);

    expect(screen.getByText('Du hast gewonnen!')).toBeTruthy();
  });

  it('sagt mir, dass ich verloren habe, wenn ich mitgespielt habe und nicht gewonnen', () => {
    renderScoring(13);

    expect(screen.getByText('Du hast verloren!')).toBeTruthy();
  });

  it('sagt Zuschauern und wer aussetzt weder Sieg noch Niederlage', () => {
    renderScoring(10);

    expect(screen.queryByText(/Du hast/)).toBeNull();
  });

  it('zeigt die Augen von Re und Kontra', () => {
    renderScoring(11);

    expect(screen.getByText('Re: 134')).toBeTruthy();
    expect(screen.getByText('Kontra: 106')).toBeTruthy();
  });

  it('listet die einzelnen Wertungspunkte mit der Summe', () => {
    renderScoring(11);

    const table = screen.getByRole('table', { name: 'Wertung' });
    const rows = within(table)
      .getAllByRole('row')
      .map((row) =>
        within(row)
          .queryAllByRole('cell')
          .map((cell) => cell.textContent?.trim())
          .join('|'),
      );
    expect(rows).toEqual(['Gewonnen|+1', 'Gegen die Alten|+1', 'Fuchs gefangen|+1', '|+3']);
  });

  it('zeigt die Gewinner und wer gewinnt', () => {
    renderScoring(11);

    const winners = screen.getByRole('list', { name: 'Gewinner' });
    expect(
      within(winners)
        .getAllByRole('listitem')
        .map((item) => item.getAttribute('aria-label')),
    ).toEqual(['Bernd', 'Clara']);
    expect(screen.getByText('Re gewinnt mit 3 Punkten!')).toBeTruthy();
  });

  it('nennt bei einem Sieg der Kontra-Partei Kontra', () => {
    renderScoring(11, endedGame({ gewinntRe: false, winners: [13], wertungsPunkte: 1 }));

    expect(screen.getByText('Kontra gewinnt mit 1 Punkt!')).toBeTruthy();
  });

  it('schreibt negative Punkte mit einem Minuszeichen', () => {
    renderScoring(
      11,
      endedGame({
        wertung: [
          ['Gewonnen', '+1'],
          ['Fuchs gefangen', '-2'],
        ],
        wertungsPunkte: -1,
      }),
    );

    const rows = within(screen.getByRole('table', { name: 'Wertung' })).getAllByRole('row');
    expect(rows[rows.length - 1].textContent?.trim()).toBe('-1');
  });
});
