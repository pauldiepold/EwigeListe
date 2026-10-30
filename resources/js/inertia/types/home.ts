import type { GamesChartPayload, GroupStatistics } from '@/types/groups-show';

export type HomeRound = {
  id: number;
  players: string;
  lastGameAt: string | null;
};

export type HomeProps = {
  group: GroupStatistics;
  currentRounds: HomeRound[];
  homeGamesChart?: GamesChartPayload;
};
