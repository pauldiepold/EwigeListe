<script setup lang="ts">
import type { RoundInfo } from '@/types/round-show';

defineProps<{
  info: RoundInfo;
}>();
</script>

<!-- Icons ersetzen die Labels; für Screenreader stehen sie als `sr-only`-Text daneben. -->
<template>
  <div
    class="flex min-h-14 flex-col justify-center gap-0.5 rounded-xl bg-elevated/60 px-3 py-1.5 text-xs text-muted"
  >
    <p v-if="info.gamesCount > 0" class="flex items-center gap-1.5">
      <UIcon name="i-lucide-hash" class="size-3.5 shrink-0" aria-hidden="true" />
      {{ info.gamesCount }} {{ info.gamesCount === 1 ? 'Spiel' : 'Spiele' }}
      <template v-if="info.averageGameMinutes !== null">
        <span aria-hidden="true">&middot;</span>
        <UIcon name="i-lucide-timer" class="size-3.5 shrink-0" aria-hidden="true" />
        <span>Ø {{ info.averageGameMinutes }} Min.<span class="sr-only"> pro Spiel</span></span>
      </template>
    </p>
    <p class="flex items-center gap-1.5">
      <UIcon name="i-lucide-play" class="size-3.5 shrink-0" aria-hidden="true" />
      <span><span class="sr-only">Gestartet von </span>{{ info.startedBy }}</span>
      <span aria-hidden="true">&middot;</span>
      <span>{{ info.startedAt }}</span>
    </p>
    <p v-if="info.lastGameAt" class="flex items-center gap-1.5">
      <UIcon name="i-lucide-pencil-line" class="size-3.5 shrink-0" aria-hidden="true" />
      <span class="sr-only">Letztes Spiel</span>
      <template v-if="info.lastGameBy">
        <span><span class="sr-only">von </span>{{ info.lastGameBy }}</span>
        <span aria-hidden="true">&middot;</span>
      </template>
      <span>{{ info.lastGameAt }}</span>
    </p>
  </div>
</template>
