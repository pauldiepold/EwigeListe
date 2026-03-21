<script setup lang="ts">
import type { Component } from 'vue';
import { computed, h, resolveComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { Column, ColumnDef, Row, SortingState } from '@tanstack/vue-table';
import type { ArchiveSortColumn, RoundArchiveRow } from '@/types/rounds-index';

const props = defineProps<{
  rows: RoundArchiveRow[];
  highlightPlayerId: number;
  sortColumn: ArchiveSortColumn;
  sortDirection: 'asc' | 'desc';
}>();

const emit = defineEmits<{
  'update:sort': [column: ArchiveSortColumn, direction: 'asc' | 'desc'];
}>();

/** Server sortiert; TanStack nur für UI-State — Setter ignoriert interne Sort-Updates. */
const tableSorting = computed<SortingState>({
  get: () => [{ id: props.sortColumn, desc: props.sortDirection === 'desc' }],
  set: () => {},
});

function sortIcon(column: Column<RoundArchiveRow, unknown>): string {
  const state = column.getIsSorted();
  if (state === 'asc') {
    return 'i-lucide-arrow-up-narrow-wide';
  }
  if (state === 'desc') {
    return 'i-lucide-arrow-down-narrow-wide';
  }
  return 'i-lucide-arrow-up-down';
}

function onSortHeaderClick(columnId: ArchiveSortColumn): void {
  if (props.sortColumn === columnId) {
    emit('update:sort', columnId, props.sortDirection === 'asc' ? 'desc' : 'asc');
    return;
  }
  emit('update:sort', columnId, 'desc');
}

const columns: ColumnDef<RoundArchiveRow>[] = [
  {
    id: 'date',
    accessorKey: 'date',
    header: 'Datum',
  },
  {
    id: 'games',
    accessorKey: 'games_count',
    header: 'Spiele',
  },
  {
    id: 'online',
    accessorKey: 'has_live_round',
    header: 'Online',
    meta: {
      class: {
        td: 'text-center align-middle',
      },
    },
    cell: ({ row }) => {
      if (!row.original.has_live_round) {
        return null;
      }
      const UIcon = resolveComponent('UIcon') as Component;
      return h(UIcon, {
        name: 'i-lucide-dice-5',
        class: 'size-6 shrink-0 align-middle text-primary',
        'aria-hidden': true,
      });
    },
  },
  {
    id: 'players',
    accessorFn: (row) => row.players_label,
    header: 'Teilnehmende Personen',
    enableSorting: false,
  },
];

const tableMeta = computed(() => ({
  class: {
    tr: (row: Row<RoundArchiveRow>) =>
      row.original.player_ids.includes(props.highlightPlayerId) ? 'bg-primary-light' : '',
  },
}));
</script>

<template>
  <div class="overflow-x-auto">
    <UTable
      v-model:sorting="tableSorting"
      :sorting-options="{ manualSorting: true }"
      :data="rows"
      :columns="columns"
      :meta="tableMeta"
      :get-row-id="(row: RoundArchiveRow) => String(row.id)"
      class="w-full max-w-5xl md:max-w-7xl"
    >
      <template #date-header="{ column }">
        <UButton
          color="neutral"
          variant="ghost"
          class="-mx-2.5"
          label="Datum"
          :trailing-icon="sortIcon(column)"
          @click="onSortHeaderClick('date')"
        />
      </template>

      <template #games-header="{ column }">
        <UButton
          color="neutral"
          variant="ghost"
          class="-mx-2.5"
          label="Spiele"
          :trailing-icon="sortIcon(column)"
          @click="onSortHeaderClick('games')"
        />
      </template>

      <template #online-header="{ column }">
        <UButton
          color="neutral"
          variant="ghost"
          class="-mx-2.5"
          label="Online"
          :trailing-icon="sortIcon(column)"
          @click="onSortHeaderClick('online')"
        />
      </template>

      <template #players-cell="{ row }">
        <Link :href="row.original.path" class="text-primary hover:underline">
          {{ row.original.players_label }}
        </Link>
      </template>
    </UTable>
  </div>
</template>
