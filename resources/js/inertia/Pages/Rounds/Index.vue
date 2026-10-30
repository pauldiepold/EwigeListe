<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import RoundArchive from '@/Components/Rounds/RoundArchive.vue';
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

      <RoundArchive
        :rounds="rounds"
        :archive-sort="archiveSort"
        @update:page="goToPage"
        @update:sort="onArchiveSortChange"
      />
    </div>
  </AppLayout>
</template>
