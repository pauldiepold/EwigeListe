export type RoundPlayer = {
  id: number;
  surname: string;
  /** `null`, wenn der Betrachter das Profil nicht sehen darf. */
  path: string | null;
  avatarPath: string | null;
  isDealer: boolean;
  isActive: boolean;
};

/** Aktiver Spieler im Eintragen-Dialog, bereits aus Sicht des Betrachters am Tisch sortiert. */
export type RoundActivePlayer = {
  id: number;
  name: string;
  avatarPath: string | null;
};

/** Das letzte Spiel als Vorbelegung zum Bearbeiten, mit den Spielern und dem Sitzplatz von damals. */
export type RoundLastGame = {
  id: number;
  points: number;
  isMisplay: boolean;
  winnerIds: number[];
  /** Ändert sich mit jeder inhaltlichen Änderung des Spiels. */
  signature: string;
  viewerIsActive: boolean;
  activePlayers: RoundActivePlayer[];
};

export type RoundGameScore = {
  /** Punktestand nach diesem Spiel; `null`, wenn der Spieler ausgesetzt hat. */
  points: number | null;
  won: boolean;
};

export type RoundGame = {
  id: number;
  points: number;
  isSolo: boolean;
  isMisplay: boolean;
  endsDealerCycle: boolean;
  scores: RoundGameScore[];
};

export type RoundInfo = {
  gamesCount: number;
  averageGameDuration: string | null;
  startedBy: string;
  startedAt: string;
  lastGameBy: string | null;
  lastGameAt: string | null;
};

export type RoundList = {
  id: number;
  name: string;
  selected: boolean;
  locked: boolean;
};

export type RoundShow = {
  id: number;
  isLive: boolean;
  canUpdate: boolean;
  viewerIsActive: boolean;
  hasChart: boolean;
  lastGameId: number | null;
  lastGame: RoundLastGame | null;
  players: RoundPlayer[];
  activePlayers: RoundActivePlayer[];
  games: RoundGame[];
  info: RoundInfo;
  lists: RoundList[];
};

export type RoundChartPayload = {
  names: string[];
  points: number[][];
};

export type RoundShowProps = {
  round: RoundShow;
  roundChart?: RoundChartPayload;
};
