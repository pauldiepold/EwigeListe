<script setup lang="ts">
import type { Component } from 'vue';
import { computed, h, resolveComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { ColumnDef, Row, SortingState } from '@tanstack/vue-table';
import { sortIcon, sortIconClass } from '@/composables/tableSorting';
import type { ArchiveSortColumn, RoundArchiveRow } from '@/types/rounds-index';

const props = defineProps<{
  rows: RoundArchiveRow[];
  highlightPlayerId: number | null;
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
    cell: ({ row }) => {
      if (!row.original.has_live_round) {
        return null;
      }
      const UIcon = resolveComponent('UIcon') as Component;
      return h(UIcon, {
        name: 'i-lucide-wifi',
        class: 'size-5 shrink-0 align-middle text-primary',
        'aria-label': 'Online',
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
      props.highlightPlayerId !== null && row.original.player_ids.includes(props.highlightPlayerId)
        ? 'bg-primary/15'
        : '',
  },
}));
</script>

<template>
  <!-- Am Handy bis an den Rand wie die Spieltabelle: hebt das Seiten-Padding auf. -->
  <div class="-mx-4 overflow-x-auto sm:mx-0">
    <UTable
      v-model:sorting="tableSorting"
      :sorting-options="{ manualSorting: true }"
      :data="rows"
      :columns="columns"
      :meta="tableMeta"
      :get-row-id="(row: RoundArchiveRow) => String(row.id)"
      :ui="{
        th: 'px-2 py-2',
        td: 'px-2 py-2.5 tabular-nums last:w-full last:whitespace-normal',
      }"
      class="w-full"
    >
      <template #date-header="{ column }">
        <UButton
          color="neutral"
          variant="ghost"
          class="-mx-2.5"
          label="Datum"
          :trailing-icon="sortIcon(column)"
          :ui="{ trailingIcon: sortIconClass(column) }"
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
          :ui="{ trailingIcon: sortIconClass(column) }"
          @click="onSortHeaderClick('games')"
        />
      </template>

      <template #online-header="{ column }">
        <UButton
          color="neutral"
          variant="ghost"
          class="-mx-2.5"
          icon="i-lucide-wifi"
          aria-label="Online"
          :trailing-icon="sortIcon(column)"
          :ui="{ trailingIcon: sortIconClass(column) }"
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
