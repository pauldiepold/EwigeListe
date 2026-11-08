import type { ArchiveSort, PaginatedRoundArchiveRows } from '@/types/rounds-index';

export type RecordHolder = {
  firstName: string;
  path: string | null;
};

export type RecordRow = {
  label: string;
  value: string;
  holders: RecordHolder[];
};

export type StatRow = {
  label: string;
  value: string;
  hint?: string;
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
  fullName: string;
  firstName: string;
  path: string | null;
  avatarPath: string | null;
  games: number;
  points: number;
  pointsPerGame: number;
  soli: number;
};

export type BadgeType = 'points' | 'games';

export type BadgeWinner = {
  firstName: string;
  value: number;
  path: string | null;
};

/** Ein Monat mit mindestens einem Abzeichen; `month` zählt ab 1. */
export type BadgeMonth = { month: number } & Record<BadgeType, BadgeWinner | null>;

export type BadgeYear = {
  year: number;
  months: BadgeMonth[];
};

export type GroupShowProps = {
  group: {
    id: number;
    name: string;
    closed: boolean;
  };
  members: GroupMember[];
  statistics: GroupStatistics;
  badges: BadgeYear[];
  rounds: PaginatedRoundArchiveRows;
  archiveSort: ArchiveSort;
  hasGamesChart: boolean;
  gamesChart?: GamesChartPayload;
  canJoin: boolean;
  canLeave: boolean;
  canClose: boolean;
};
