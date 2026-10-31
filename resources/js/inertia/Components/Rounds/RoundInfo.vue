<script setup lang="ts">
import type { RoundInfo } from '@/types/round-show';

defineProps<{
  info: RoundInfo;
}>();
</script>

<template>
  <div class="space-y-1 text-center text-sm text-muted">
    <p v-if="info.gamesCount > 0">Anzahl Spiele: {{ info.gamesCount }}</p>
    <p v-if="info.averageGameDuration">
      Durchschnittliche Spieldauer: {{ info.averageGameDuration }}
    </p>
    <p>Runde gestartet von {{ info.startedBy }} {{ info.startedAt }}.</p>
    <p v-if="info.lastGameBy">
      Letztes Spiel eingetragen von {{ info.lastGameBy }} {{ info.lastGameAt }}.
    </p>
  </div>
</template>
