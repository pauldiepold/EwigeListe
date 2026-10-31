<script setup lang="ts">
import { computed } from 'vue';
import { WhenVisible } from '@inertiajs/vue3';
import GamesChart from '@/Components/Groups/GamesChart.vue';
import GamesChartLoading from '@/Components/Groups/GamesChartLoading.vue';
import LineChart from '@/Components/Ui/LineChart.vue';
import type { ProfileChartPayload } from '@/types/players-show';

const props = defineProps<{
  chartData?: ProfileChartPayload;
}>();

const pointsSeries = computed(() =>
  props.chartData ? { labels: props.chartData.dates, values: props.chartData.points } : null,
);
</script>

<template>
  <WhenVisible data="profileChart">
    <template #fallback>
      <GamesChartLoading />
    </template>

    <section v-if="chartData && pointsSeries" class="space-y-6">
      <div>
        <h2 class="mb-4 text-xl font-semibold text-highlighted">Punkteverlauf</h2>
        <LineChart :series="pointsSeries" />
      </div>

      <div>
        <h2 class="mb-4 text-xl font-semibold text-highlighted">Anzahl der Spiele</h2>
        <GamesChart :chart-data="chartData" />
      </div>
    </section>
  </WhenVisible>
</template>
