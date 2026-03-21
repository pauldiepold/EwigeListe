export type RecordRow = {
  labelHtml: string;
  value: string;
  detailHtml: string;
};

export type StatRow = {
  labelHtml: string;
  value: string;
};

export type HomeGamesChartPayload = {
  gameDates: string[];
  gameCounter: number[];
};

export type HomeGroup = {
  id: number;
  records: RecordRow[];
  stats: StatRow[];
};

export type HomeRound = {
  id: number;
  path: string;
  players: string;
  lastGameAt: string | null;
};
