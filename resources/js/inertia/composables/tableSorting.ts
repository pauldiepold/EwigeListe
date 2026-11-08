import type { Column } from '@tanstack/vue-table';

/** Pfeil im Kopf einer sortierbaren Tabellenspalte. */
export function sortIcon<TData>(column: Column<TData, unknown>): string {
  const state = column.getIsSorted();
  if (state === 'asc') {
    return 'i-lucide-arrow-up-narrow-wide';
  }
  if (state === 'desc') {
    return 'i-lucide-arrow-down-narrow-wide';
  }
  return 'i-lucide-arrow-up-down';
}

/**
 * Der Pfeil tritt hinter die Beschriftung zurück, bei der aktiven Sortierung etwas weniger.
 * Am Handy zeigt nur die aktive Spalte ihren Pfeil, sonst passt die Tabelle nicht in die Breite.
 */
export function sortIconClass<TData>(column: Column<TData, unknown>): string {
  return column.getIsSorted() ? 'text-muted' : 'text-dimmed max-sm:hidden';
}
