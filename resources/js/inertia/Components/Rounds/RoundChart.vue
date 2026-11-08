<script setup lang="ts">
import { computed, inject, onBeforeUnmount, ref, watch } from 'vue';
import Chart from 'chart.js/auto';
import { inertiaColorModeKey } from '@/composables/inertiaColorMode';
import type { RoundChartPayload } from '@/types/round-show';

const SERIES_COLORS = ['#E37222', '#07889B', '#24980C', '#C70E00', '#481620', '#e22fe3', '#5d0be3'];

const props = defineProps<{
  chartData: RoundChartPayload;
}>();

const colorMode = inject(inertiaColorModeKey);
const textColor = computed(() => (colorMode?.value === 'dark' ? '#94a3b8' : '#475569'));

const canvasRef = ref<HTMLCanvasElement | null>(null);
let chart: Chart | null = null;

function render(canvas: HTMLCanvasElement): void {
  chart?.destroy();

  chart = new Chart(canvas, {
    type: 'line',
    data: {
      labels: props.chartData.points[0]?.map((_, gameNumber) => gameNumber) ?? [],
      datasets: props.chartData.names.map((name, seat) => ({
        label: name,
        data: props.chartData.points[seat] ?? [],
        borderColor: SERIES_COLORS[seat],
        backgroundColor: SERIES_COLORS[seat],
        tension: 0.1,
      })),
    },
    options: {
      animation: false,
      maintainAspectRatio: false,
      color: textColor.value,
      scales: {
        x: {
          ticks: { color: textColor.value },
          title: { display: true, text: 'Spiele', color: textColor.value },
          border: { display: false },
        },
        y: {
          ticks: { color: textColor.value },
          title: { display: true, text: 'Punkte', color: textColor.value },
          border: { display: false },
        },
      },
      plugins: {
        legend: {
          labels: { boxWidth: 10, usePointStyle: true, font: { size: 13 }, padding: 20 },
        },
      },
      elements: {
        point: { radius: 0, hitRadius: 10 },
        line: { borderWidth: 1.8, fill: false },
      },
    },
  });
}

watch(
  [canvasRef, () => props.chartData, textColor],
  ([canvas]) => {
    if (canvas) {
      render(canvas);
    }
  },
  { immediate: true },
);

onBeforeUnmount(() => {
  chart?.destroy();
});
</script>

<template>
  <div class="mx-auto h-[450px] max-w-xl">
    <canvas ref="canvasRef" />
  </div>
</template>
