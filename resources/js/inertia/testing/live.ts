import type {
  LiveCardRank,
  LiveCardSuit,
  LiveEngineCard,
  LiveGameData,
  LivePlayer,
  LivePlayerDisplay,
  LiveSpieler,
} from '@/types/live';

/** Ein Spieler am Tisch; `id` ist die Spieler-ID, `isActive: false` setzt ihn im Spiel aus. */
export function livePlayer(id: number, firstName: string, isActive = true): LivePlayer {
  return {
    id,
    firstName,
    lastName: 'Test',
    fullName: `${firstName} Test`,
    avatarPath: null,
    isActive,
  };
}

/** Die Anzeige eines Spielers am Tisch, solange noch nichts feststeht. */
export function liveDisplay(): LivePlayerDisplay {
  return { ansage: '', spieltyp: '', partei: '', vorbehalt: '' };
}

/** Eine spielbare Karte auf der Hand (Kreuz Dame, wenn nichts anderes gesagt wird). */
export function liveCard(
  id: number,
  farbe: LiveCardSuit = 4,
  wert: LiveCardRank = 3,
  overrides: Partial<LiveEngineCard> = {},
): LiveEngineCard {
  return {
    id,
    farbe,
    wert,
    rang: id,
    punkte: 0,
    trumpf: false,
    spielbar: true,
    gespieltVon: false,
    armut_zurueck: false,
    ...overrides,
  };
}

/** Eine Karte, die `by` gelegt hat. */
export function playedCard(
  id: number,
  farbe: LiveCardSuit,
  wert: LiveCardRank,
  by: number,
): LiveEngineCard {
  return liveCard(id, farbe, wert, { gespieltVon: by, spielbar: false });
}

/** Ein laufendes Spiel (Phase 4) der Spieler 11 bis 14, Spieler 11 ist dran und Vorhand. */
export function liveGame(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return {
    id: 1,
    phase: 4,
    dran: 11,
    stichNr: 1,
    vorhand: 11,
    aktuellerStich: { karten: [], stecher: null },
    letzterStich: { karten: [], stecher: null },
    spieler: { 11: liveDisplay(), 12: liveDisplay(), 13: liveDisplay(), 14: liveDisplay() },
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

/** Der eigene Spieler (`ich`), standardmäßig Spieler 12 mit leerer Hand. */
export function liveSpieler(overrides: Partial<LiveSpieler> = {}): LiveSpieler {
  return {
    id: 12,
    index: 0,
    hand: [],
    armutKarten: [],
    moeglicheVorbehalte: [],
    vorbehalt: null,
    isRe: null,
    ansage: null,
    absage: null,
    moeglicheAnAbsage: null,
    punkte: null,
    parteiOffengelegt: 0,
    ...overrides,
  };
}
