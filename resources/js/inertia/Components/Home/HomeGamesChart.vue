<script setup lang="ts">
import { ref } from 'vue';
import { useHomeChart } from '@/composables/useHomeChart';

const props = defineProps<{
  groupId: number;
}>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const { isLoading, error } = useHomeChart(canvasRef, props.groupId);
</script>

<template>
  <div class="mx-auto max-w-3xl rounded border border-slate-200 bg-white p-4">
    <p v-if="isLoading" class="mb-3 text-sm text-slate-600">Chart wird geladen...</p>
    <p v-else-if="error" class="mb-3 text-sm text-red-600">{{ error }}</p>
    <div class="h-[360px]">
      <canvas ref="canvasRef" />
    </div>
  </div>
</template>
