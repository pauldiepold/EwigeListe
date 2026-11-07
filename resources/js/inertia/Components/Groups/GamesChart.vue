<script setup lang="ts">
import { computed } from 'vue';
import LineChart from '@/Components/Ui/LineChart.vue';
import type { GamesChartPayload } from '@/types/groups-show';

const props = defineProps<{
  chartData: GamesChartPayload;
}>();

const series = computed(() => ({
  labels: props.chartData.gameDates,
  values: props.chartData.gameCounter,
}));
</script>

<template>
  <LineChart :series="series" />
</template>
