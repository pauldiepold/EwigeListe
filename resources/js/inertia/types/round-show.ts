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
  lastGameId: number | null;
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
