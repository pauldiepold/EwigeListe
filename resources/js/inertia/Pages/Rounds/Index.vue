<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ArchiveRoundsTable from '@/Components/Rounds/ArchiveRoundsTable.vue';
import { useAppRoute } from '@/composables/useAppRoute';
import type { ArchiveSort, ArchiveSortColumn, GroupOption, PaginatedRoundArchiveRows } from '@/types/rounds-index';

const props = defineProps<{
  groupOptions: GroupOption[];
  selectedGroup: GroupOption;
  rounds: PaginatedRoundArchiveRows;
  loggedInPlayerId: number;
  archiveSort: ArchiveSort;
}>();

const { route } = useAppRoute();

function roundsIndexParams(overrides: Record<string, string | number> = {}): Record<string, string | number> {
  return {
    group: props.selectedGroup.id,
    sort: props.archiveSort.column,
    direction: props.archiveSort.direction,
    ...overrides,
  };
}

function onGroupChange(groupId: string): void {
  const id = Number(groupId);
  if (!id || id === props.selectedGroup.id) {
    return;
  }
  router.visit(route('rounds.index', roundsIndexParams({ group: id })), {
    preserveScroll: true,
    preserveState: false,
  });
}

function goToPage(page: number): void {
  if (page === props.rounds.current_page || page < 1 || page > props.rounds.last_page) {
    return;
  }
  router.visit(route('rounds.index', roundsIndexParams({ page })), {
    preserveScroll: true,
    preserveState: false,
  });
}

function onArchiveSortChange(column: ArchiveSortColumn, direction: 'asc' | 'desc'): void {
  router.visit(route('rounds.index', roundsIndexParams({ sort: column, direction, page: 1 })), {
    preserveScroll: true,
    preserveState: false,
  });
}
</script>

<template>
  <Head title="Rundenarchiv" />

  <AppLayout title="Rundenarchiv">
    <div class="mx-auto w-full max-w-5xl space-y-6 md:max-w-7xl">
      <UFormField label="Liste">
        <select
          class="focus:ring-primary w-full max-w-md rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-1 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
          :value="selectedGroup.id"
          @change="onGroupChange(($event.target as HTMLSelectElement).value)"
        >
          <option v-for="group in groupOptions" :key="group.id" :value="group.id">
            {{ group.name }}
          </option>
        </select>
      </UFormField>

      <template v-if="rounds.total > 0">
        <ArchiveRoundsTable
          :rows="rounds.data"
          :highlight-player-id="loggedInPlayerId"
          :sort-column="archiveSort.column"
          :sort-direction="archiveSort.direction"
          @update:sort="onArchiveSortChange"
        />

        <p class="text-slate-600 dark:text-slate-400">{{ rounds.total }} Runden</p>

        <UPagination
          v-if="rounds.last_page > 1"
          :page="rounds.current_page"
          :total="rounds.total"
          :items-per-page="rounds.per_page"
          show-edges
          class="flex justify-center"
          @update:page="goToPage"
        />
      </template>

      <p v-else class="text-lg text-slate-700 dark:text-slate-300">Bisher wurden keine Runden gespielt.</p>
    </div>
  </AppLayout>
</template>
