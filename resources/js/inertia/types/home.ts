import type { GamesChartPayload, GroupStatistics } from '@/types/groups-show';

export type HomeRoundPlayer = {
  id: number;
  name: string;
  avatarPath: string | null;
  /** Punktestand in dieser Runde */
  points: number;
};

export type HomeRound = {
  id: number;
  players: HomeRoundPlayer[];
  gamesCount: number;
  /** ISO-8601 */
  lastGameAt: string | null;
  isLive: boolean;
  isOwn: boolean;
};

export type HomeProps = {
  group: GroupStatistics;
  currentRounds: HomeRound[];
  homeGamesChart?: GamesChartPayload;
};
