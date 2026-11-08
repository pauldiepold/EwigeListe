import type { LiveRules } from '@/Components/Live/liveRules';

/** Farbe einer Karte in der Live-Engine: 1 Karo, 2 Herz, 3 Pik, 4 Kreuz (`app/Live/Karte.php`). */
export type LiveCardSuit = 1 | 2 | 3 | 4;

/** Wert einer Karte in der Live-Engine: 1 Neun, 2 Bube, 3 Dame, 4 König, 5 Zehn, 6 Ass. */
export type LiveCardRank = 1 | 2 | 3 | 4 | 5 | 6;

/** Der Teil einer Engine-Karte, den die Darstellung braucht. */
export type LiveCard = {
  farbe: LiveCardSuit;
  wert: LiveCardRank;
};

/** Eine Karte, wie die Engine sie ausliefert (`App\Live\Karte`). */
export type LiveEngineCard = LiveCard & {
  id: number;
  rang: number;
  punkte: number;
  trumpf: boolean;
  spielbar: boolean;
  /** ID des Spielers, der die Karte gelegt hat; `false`, solange sie auf der Hand liegt. */
  gespieltVon: number | false;
  armut_zurueck: boolean;
};

/** Spieler eines Live-Tisches in Sitzreihenfolge (`LiveRoundPageResource`). */
export type LivePlayer = {
  id: number;
  firstName: string;
  lastName: string;
  fullName: string;
  avatarPath: string | null;
  /** Spielt im aktuellen (bzw. nächsten) Spiel mit, statt auszusetzen. */
  isActive: boolean;
};

export type LiveRound = {
  id: number;
  /** Adresse der Runden-Seite („Zurück zur Runde“). */
  path: string;
  gamesCount: number;
  liveRoundId: number;
  rules: LiveRules;
  viewerPlayerId: number;
  /** Alle Spieler der Runde in Sitzreihenfolge, ohne Drehung auf den Betrachter. */
  players: LivePlayer[];
};

/** Eine Meldung der Engine: Reiner Text, der Name steht nicht darin, sondern kommt aus der Spielerliste. */
export type LiveMessage = {
  spielerId: number | null;
  text: string;
};

/** Anzeige eines Spielers am Tisch (`App\Live\Anzeige`); leer, solange noch nichts feststeht. */
export type LivePlayerDisplay = {
  ansage: string;
  spieltyp: string;
  partei: string;
  vorbehalt: string;
};

export type LiveTrick = {
  karten: LiveEngineCard[];
  stecher: number | null;
};

/**
 * Das öffentliche Spiel, wie `App\Http\Resources\LiveGame` es ausliefert: als Prop der Live-Seite
 * und in den Broadcasts `LiveGameDataBroadcasted` und `LiveGameDataBroadcastedInaktiv`.
 */
export type LiveGameData = {
  id: number;
  /** 2 Vorbehalte, 3/32/33 Armut, 4 Spiel, 101 Wertung (siehe `LiveGame`). */
  phase: number;
  dran: number;
  stichNr: number;
  vorhand: number;
  aktuellerStich: LiveTrick;
  letzterStich: LiveTrick;
  /** Anzeige je Spieler-ID. */
  spieler: Record<number, LivePlayerDisplay>;
  /** Neueste zuerst. */
  messages: LiveMessage[];
  spieltyp: string;
  schweinchen: boolean;
  kontrasOffengelegt: number;
  reAnsage: boolean;
  kontraAnsage: boolean;
  reAbsage: string | null;
  kontraAbsage: string | null;
  /** Nur im beendeten Spiel. */
  winners?: number[];
  wertung?: [string, string | number][];
  reAugen?: number;
  kontraAugen?: number;
  gewinntRe?: boolean;
  wertungsPunkte?: number;
};

/** Der eigene Spieler mit Hand und Möglichkeiten (`App\Live\Spieler`), nur für Mitspieler. */
export type LiveSpieler = {
  id: number;
  index: number;
  hand: LiveEngineCard[] | Record<string, LiveEngineCard>;
  armutKarten: LiveEngineCard[] | Record<string, LiveEngineCard>;
  moeglicheVorbehalte: string[] | Record<string, string>;
  vorbehalt: string | null;
  isRe: boolean | null;
  ansage: string | null;
  absage: string | null;
  moeglicheAnAbsage: string | null;
  punkte: number | null;
  parteiOffengelegt: number;
};

/** Payload von `LiveGameDataBroadcasted` (Spieler, mit `ich`) bzw. `LiveGameDataBroadcastedInaktiv` (ohne). */
export type LiveGamePush = {
  liveGame: LiveGameData;
  ich?: LiveSpieler;
};

export type LiveShowProps = {
  round: LiveRound;
  liveGame: LiveGameData | null;
  lastLiveGame: LiveGameData | null;
  ich: LiveSpieler | null;
};
