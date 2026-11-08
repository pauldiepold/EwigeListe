import type { LiveGameData, LivePlayer } from '@/types/live';

/**
 * Die Spieler des Spiels in der Reihenfolge, in der sie am Tisch erscheinen: Platz 0 sitzt unten,
 * danach folgen links, oben und rechts. Die Anzeige beginnt bei meinem Sitz und folgt der
 * Sitzreihenfolge; setze ich aus, beginnt sie beim nächsten aktiven Sitz hinter mir. Wer nicht
 * in der Runde sitzt (Zuschauer), sieht die Sitzreihenfolge ohne Drehung.
 *
 * @param players Alle Spieler der Runde in Sitzreihenfolge.
 */
export function seatAroundViewer(players: LivePlayer[], viewerId: number | null): LivePlayer[] {
  const active = players.filter((player) => player.isActive);
  const viewerSeat = players.findIndex((player) => player.id === viewerId);

  if (viewerSeat === -1) {
    return active;
  }

  for (let offset = 0; offset < players.length; offset++) {
    const startId = players[(viewerSeat + offset) % players.length].id;
    const start = active.findIndex((player) => player.id === startId);

    if (start !== -1) {
      return [...active.slice(start), ...active.slice(0, start)];
    }
  }

  return active;
}

/**
 * Die Spieler der Runde, wobei `isActive` aus dem angezeigten Spiel stammt statt aus der Seite. Der
 * Tisch zeigt das beendete Spiel noch, während die Seite schon die Spieler des nächsten aktiv setzt
 * (ab fünf Spielern wechselt, wer aussetzt). Ohne Spiel gilt die Seite.
 */
export function playersOfGame(players: LivePlayer[], liveGame: LiveGameData | null): LivePlayer[] {
  if (liveGame === null) {
    return players;
  }

  const gamePlayerIds = Object.keys(liveGame.spieler).map(Number);

  return players.map((player) => ({ ...player, isActive: gamePlayerIds.includes(player.id) }));
}
