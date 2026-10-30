<script setup lang="ts">
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ArchiveRoundsTable from '@/Components/Rounds/ArchiveRoundsTable.vue';
import { index as roundsIndex } from '@/routes/rounds';
import type {
  ArchiveSort,
  ArchiveSortColumn,
  GroupOption,
  PaginatedRoundArchiveRows,
} from '@/types/rounds-index';

const props = defineProps<{
  groupOptions: GroupOption[];
  selectedGroup: GroupOption;
  rounds: PaginatedRoundArchiveRows;
  archiveSort: ArchiveSort;
}>();

const page = usePage();
const authPlayerId = computed(() => page.props.auth.player?.id ?? null);

type RoundsIndexQuery = {
  sort?: ArchiveSortColumn;
  direction?: 'asc' | 'desc';
  page?: number;
};

function roundsIndexUrl(groupId: number, query: RoundsIndexQuery = {}): string {
  return roundsIndex.url(groupId, {
    query: {
      sort: props.archiveSort.column,
      direction: props.archiveSort.direction,
      ...query,
    },
  });
}

function onGroupChange(groupId: number): void {
  if (groupId === props.selectedGroup.id) {
    return;
  }
  router.visit(roundsIndexUrl(groupId), {
    preserveScroll: true,
    preserveState: false,
  });
}

function goToPage(page: number): void {
  if (page === props.rounds.current_page || page < 1 || page > props.rounds.last_page) {
    return;
  }
  router.visit(roundsIndexUrl(props.selectedGroup.id, { page }), {
    preserveScroll: true,
    preserveState: false,
  });
}

function onArchiveSortChange(column: ArchiveSortColumn, direction: 'asc' | 'desc'): void {
  router.visit(roundsIndexUrl(props.selectedGroup.id, { sort: column, direction, page: 1 }), {
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
        <USelect
          :model-value="selectedGroup.id"
          :items="groupOptions"
          value-key="id"
          label-key="name"
          class="w-full max-w-md"
          @update:model-value="onGroupChange"
        />
      </UFormField>

      <template v-if="rounds.total > 0">
        <ArchiveRoundsTable
          :rows="rounds.data"
          :highlight-player-id="authPlayerId"
          :sort-column="archiveSort.column"
          :sort-direction="archiveSort.direction"
          @update:sort="onArchiveSortChange"
        />

        <p class="text-muted">{{ rounds.total }} Runden</p>

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

      <p v-else class="text-lg text-toned">Bisher wurden keine Runden gespielt.</p>
    </div>
  </AppLayout>
</template>
