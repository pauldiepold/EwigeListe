<script setup lang="ts">
import type { RoundInfo } from '@/types/round-show';

defineProps<{
  info: RoundInfo;
}>();
</script>

<template>
  <div class="space-y-0.5 text-xs text-muted">
    <p v-if="info.gamesCount > 0">
      {{ info.gamesCount }} {{ info.gamesCount === 1 ? 'Spiel' : 'Spiele' }}
      <template v-if="info.averageGameMinutes !== null">
        &middot; Ø {{ info.averageGameMinutes }} Min. pro Spiel
      </template>
    </p>
    <!-- Am Handy steht das letzte Spiel in einer eigenen Zeile, sonst bricht die Zeile mittendrin um. -->
    <p class="flex flex-col sm:flex-row sm:gap-1">
      <span>Gestartet von {{ info.startedBy }}, {{ info.startedAt }}</span>
      <template v-if="info.lastGameAt">
        <span class="hidden sm:inline" aria-hidden="true">&middot;</span>
        <span>
          Letztes Spiel<template v-if="info.lastGameBy"> von {{ info.lastGameBy }}</template
          >,
          {{ info.lastGameAt }}
        </span>
      </template>
    </p>
  </div>
</template>
