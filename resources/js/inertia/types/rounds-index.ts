export type ArchiveSortColumn = 'date' | 'games' | 'online';

export type ArchiveSort = {
  column: ArchiveSortColumn;
  direction: 'asc' | 'desc';
};

export type RoundArchiveRow = {
  id: number;
  path: string;
  date: string;
  games_count: number;
  has_live_round: boolean;
  player_ids: number[];
  players_label: string;
};

/** Shape of `LengthAwarePaginator::toArray()` (Inertia JSON). */
export type PaginatedRoundArchiveRows = {
  data: RoundArchiveRow[];
  current_page: number;
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
};

export type GroupOption = {
  id: number;
  name: string;
};
