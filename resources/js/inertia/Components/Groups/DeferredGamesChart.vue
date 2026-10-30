<script setup lang="ts">
import { Deferred } from '@inertiajs/vue3';
import GamesChart from '@/Components/Groups/GamesChart.vue';
import AppLoadingIndicator from '@/Components/Ui/AppLoadingIndicator.vue';
import type { GamesChartPayload } from '@/types/groups-show';

defineProps<{
  /** Name des Deferred-Props der Page. */
  prop: string;
  chartData?: GamesChartPayload;
}>();
</script>

<template>
  <Deferred :data="prop">
    <template #fallback>
      <div class="mx-auto max-w-3xl rounded border border-default bg-elevated p-4" aria-busy="true">
        <div
          class="flex min-h-[360px] flex-col items-center justify-center gap-2 rounded bg-muted/10"
        >
          <AppLoadingIndicator label="Chart wird geladen…" size="md" />
        </div>
      </div>
    </template>

    <GamesChart v-if="chartData" :chart-data="chartData" />
  </Deferred>
</template>
