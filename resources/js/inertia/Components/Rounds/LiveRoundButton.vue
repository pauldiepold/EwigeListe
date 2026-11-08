<script setup lang="ts">
import { live } from '@/routes/rounds';

defineProps<{
  roundId: number;
  /** Spielt im aktuellen Spiel mit; alle anderen sehen zu und bekommen keine Karten. */
  viewerIsActive: boolean;
}>();
</script>

<template>
  <UButton
    :to="live.url(roundId)"
    size="xl"
    :icon="viewerIsActive ? 'i-lucide-play' : 'i-lucide-eye'"
    :label="viewerIsActive ? 'Live spielen' : 'Zuschauen'"
  />
</template>
