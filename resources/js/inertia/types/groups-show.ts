import type { ArchiveSort, PaginatedRoundArchiveRows } from '@/types/rounds-index';

export type RecordHolder = {
  name: string;
  path: string;
};

export type RecordRow = {
  label: string;
  value: string;
  holders: RecordHolder[];
};

export type StatRow = {
  label: string;
  value: string;
};

export type GroupStatistics = {
  records: RecordRow[];
  stats: StatRow[];
};

export type GamesChartPayload = {
  gameDates: string[];
  gameCounter: number[];
};

export type GroupMember = {
  id: number;
  name: string;
  surname: string;
  path: string;
  avatarPath: string;
  games: number;
  points: number;
  pointsPerGame: number;
  soli: number;
};

export type BadgeType = 'points' | 'games';

export type GroupBadge = {
  name: string;
  value: number;
  date: string;
  path: string;
};

export type GroupBadgesOfType = {
  type: BadgeType;
  years: Array<{
    year: number;
    badges: GroupBadge[];
  }>;
};

export type GroupShowProps = {
  group: {
    id: number;
    name: string;
    closed: boolean;
  };
  members: GroupMember[];
  statistics: GroupStatistics;
  badges: GroupBadgesOfType[];
  rounds: PaginatedRoundArchiveRows;
  archiveSort: ArchiveSort;
  hasGamesChart: boolean;
  gamesChart?: GamesChartPayload;
  canJoin: boolean;
  canLeave: boolean;
  canClose: boolean;
};
