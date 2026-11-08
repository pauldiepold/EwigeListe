import { describe, expect, it } from 'vitest';
import { playersOfGame, seatAroundViewer } from '@/Components/Live/liveSeating';
import type { LiveGameData, LivePlayer } from '@/types/live';

function table(activeSeats: number[], count = activeSeats.length): LivePlayer[] {
  return Array.from({ length: count }, (_, seat) => ({
    id: 100 + seat,
    firstName: `Spieler ${seat}`,
    lastName: 'Test',
    fullName: `Spieler ${seat} Test`,
    avatarPath: null,
    isActive: activeSeats.includes(seat),
  }));
}

function ids(players: LivePlayer[]): number[] {
  return players.map((player) => player.id);
}

describe('seatAroundViewer', () => {
  it('setzt mich unten und die anderen in Sitzreihenfolge', () => {
    const players = table([0, 1, 2, 3]);

    expect(ids(seatAroundViewer(players, 102))).toEqual([102, 103, 100, 101]);
  });

  it('lässt die Sitzreihenfolge unverändert, wenn ich auf Platz 1 sitze', () => {
    const players = table([0, 1, 2, 3]);

    expect(ids(seatAroundViewer(players, 100))).toEqual([100, 101, 102, 103]);
  });

  it('zeigt bei fünf Spielern nur die vier aktiven, ich bleibe unten', () => {
    // Sitz 0 gibt und setzt aus.
    const players = table([1, 2, 3, 4], 5);

    expect(ids(seatAroundViewer(players, 103))).toEqual([103, 104, 101, 102]);
  });

  it('beginnt beim nächsten aktiven Sitz, wenn ich selbst aussetze', () => {
    const players = table([1, 2, 3, 4], 5);

    expect(ids(seatAroundViewer(players, 100))).toEqual([101, 102, 103, 104]);
  });

  it('springt über mehrere aussetzende Sitze und über das Ende der Runde hinweg', () => {
    // Sieben Spieler: Sitze 2 und 4 setzen aus, dazu der Geber auf Sitz 6... hier nur 1, 3, 5, 6 aktiv.
    const players = table([1, 3, 5, 6], 7);

    expect(ids(seatAroundViewer(players, 102))).toEqual([103, 105, 106, 101]);
    expect(ids(seatAroundViewer(players, 104))).toEqual([105, 106, 101, 103]);
    expect(ids(seatAroundViewer(players, 106))).toEqual([106, 101, 103, 105]);
  });

  it('zeigt Zuschauern die Sitzreihenfolge ohne Drehung', () => {
    const players = table([1, 2, 3, 4], 5);

    expect(ids(seatAroundViewer(players, 999))).toEqual([101, 102, 103, 104]);
    expect(ids(seatAroundViewer(players, null))).toEqual([101, 102, 103, 104]);
  });
});

describe('playersOfGame', () => {
  const spieler = (...ids: number[]) =>
    ({ spieler: Object.fromEntries(ids.map((id) => [id, {}])) }) as unknown as LiveGameData;

  it('übernimmt, wer im Spiel mitspielt, nicht was die Seite als aktiv führt', () => {
    // Die Seite setzt schon Sitz 4 aus, im angezeigten Spiel spielt aber noch Sitz 4 und setzt Sitz 0 aus.
    const players = table([0, 1, 2, 3], 5);

    const result = playersOfGame(players, spieler(101, 102, 103, 104));

    expect(result.map((player) => player.isActive)).toEqual([false, true, true, true, true]);
  });

  it('lässt die Seite gelten, solange kein Spiel angezeigt wird', () => {
    const players = table([0, 1, 2, 3], 5);

    expect(playersOfGame(players, null)).toBe(players);
  });

  it('behält die Sitzreihenfolge', () => {
    const players = table([0, 1, 2, 3], 5);

    expect(ids(playersOfGame(players, spieler(101, 102, 103, 104)))).toEqual(ids(players));
  });
});
