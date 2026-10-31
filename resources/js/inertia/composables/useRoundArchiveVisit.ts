import { router } from '@inertiajs/vue3';
import type { ArchiveSort, ArchiveSortColumn } from '@/types/rounds-index';

type ArchiveQuery = {
  sort: ArchiveSortColumn;
  direction: 'asc' | 'desc';
  page: number;
};

/**
 * Blättern und Sortieren eines in die Seite eingebetteten Rundenarchivs: lädt nur das Archiv neu.
 */
export function useRoundArchiveVisit(
  archiveSort: () => ArchiveSort,
  urlFor: (query: ArchiveQuery) => string,
): {
  onArchivePageChange: (targetPage: number) => void;
  onArchiveSortChange: (column: ArchiveSortColumn, direction: 'asc' | 'desc') => void;
} {
  function visitArchive(query: ArchiveQuery): void {
    router.visit(urlFor(query), {
      only: ['rounds', 'archiveSort'],
      preserveScroll: true,
      preserveState: true,
    });
  }

  function onArchivePageChange(targetPage: number): void {
    const { column, direction } = archiveSort();
    visitArchive({ sort: column, direction, page: targetPage });
  }

  function onArchiveSortChange(column: ArchiveSortColumn, direction: 'asc' | 'desc'): void {
    visitArchive({ sort: column, direction, page: 1 });
  }

  return { onArchivePageChange, onArchiveSortChange };
}
