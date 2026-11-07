<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ArchiveRoundsTable from '@/Components/Rounds/ArchiveRoundsTable.vue';
import type {
  ArchiveSort,
  ArchiveSortColumn,
  PaginatedRoundArchiveRows,
} from '@/types/rounds-index';

const props = withDefaults(
  defineProps<{
    rounds: PaginatedRoundArchiveRows;
    archiveSort: ArchiveSort;
    highlightOwnRounds?: boolean;
  }>(),
  { highlightOwnRounds: true },
);

const emit = defineEmits<{
  'update:page': [page: number];
  'update:sort': [column: ArchiveSortColumn, direction: 'asc' | 'desc'];
}>();

const page = usePage();
const authPlayerId = computed(() => page.props.auth.player?.id ?? null);

function goToPage(targetPage: number): void {
  if (
    targetPage === props.rounds.current_page ||
    targetPage < 1 ||
    targetPage > props.rounds.last_page
  ) {
    return;
  }
  emit('update:page', targetPage);
}
</script>

<template>
  <div v-if="rounds.total > 0" class="space-y-6">
    <ArchiveRoundsTable
      :rows="rounds.data"
      :highlight-player-id="highlightOwnRounds ? authPlayerId : null"
      :sort-column="archiveSort.column"
      :sort-direction="archiveSort.direction"
      @update:sort="(column, direction) => emit('update:sort', column, direction)"
    />

    <!-- Erste und letzte Seite stehen per `show-edges` schon als Zahl da, die Doppelpfeile sparen am Handy Platz. -->
    <UPagination
      v-if="rounds.last_page > 1"
      :page="rounds.current_page"
      :total="rounds.total"
      :items-per-page="rounds.per_page"
      :sibling-count="1"
      show-edges
      :ui="{ first: 'hidden', last: 'hidden' }"
      class="flex justify-center"
      @update:page="goToPage"
    />

    <p class="text-center text-muted">{{ rounds.total }} Runden</p>
  </div>

  <p v-else class="text-lg text-toned">Bisher wurden keine Runden gespielt.</p>
</template>
