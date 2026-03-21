<script setup lang="ts">
import { onMounted, ref, toRef } from 'vue';
import { useHomeChart } from '@/composables/useHomeChart';
import type { HomeGamesChartPayload } from '@/types/home';

const props = defineProps<{
  chartData: HomeGamesChartPayload;
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const { error } = useHomeChart(canvasRef, toRef(props, 'chartData'));

/** Sanftes Einblenden nach dem Deferred-Load (unabhängig von Chart.js). */
const surfaceEntered = ref(false);

onMounted(() => {
  requestAnimationFrame(() => {
    surfaceEntered.value = true;
  });
});
</script>

<template>
  <div class="mx-auto max-w-3xl rounded border border-default bg-elevated p-4">
    <p v-if="error" class="mb-3 text-sm text-error">{{ error }}</p>
    <div
      class="h-[360px] transition-[opacity,transform] duration-700 ease-out motion-reduce:transition-none"
      :class="
        surfaceEntered ? 'translate-y-0 opacity-100' : 'translate-y-2 opacity-0'
      "
    >
      <canvas ref="canvasRef" />
    </div>
  </div>
</template>
