import type { GamesChartPayload, GroupBadgesOfType, StatRow } from '@/types/groups-show';
import type { ArchiveSort, GroupOption, PaginatedRoundArchiveRows } from '@/types/rounds-index';

export type ProfileStatistics = {
  joinedLabel: string;
  statistics: StatRow[][] | null;
  opportunityCosts: StatRow[] | null;
};

export type ProfileChartPayload = GamesChartPayload & {
  dates: string[];
  points: number[];
};

export type PlayerShowProps = {
  player: {
    id: number;
    name: string;
    avatarPath: string | null;
    editableUserId: number | null;
  };
  groupOptions: GroupOption[];
  selectedGroupId: number;
  profile: ProfileStatistics;
  badges: GroupBadgesOfType[];
  badgeCount: number;
  rounds: PaginatedRoundArchiveRows;
  archiveSort: ArchiveSort;
  profileChart?: ProfileChartPayload;
};
