<script setup lang="ts">
import type { RoundInfo } from '@/types/round-show';

defineProps<{
  info: RoundInfo;
}>();

defineSlots<{
  /** Aktionen zum letzten Spiel, rechts in dessen Zeile. */
  lastGameActions?: () => unknown;
}>();
</script>

<!-- Icons ersetzen die Labels; für Screenreader stehen sie als `sr-only`-Text daneben. -->
<template>
  <div
    class="flex min-h-14 flex-col justify-center gap-1 rounded-lg bg-elevated/60 px-3 py-2 text-xs text-muted"
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
    <!-- Als einzige bedienbare Zeile mit Text: Die Aktionen beziehen sich auf das letzte Spiel. -->
    <!--
      Grid statt umbrechendem Flex: Dessen Wunschbreite zählte Text und Rückfrage nebeneinander,
      die Kachel würde breiter als nötig.
    -->
    <div v-if="info.lastGameAt" class="grid grid-cols-[1fr_auto] items-center gap-x-3 gap-y-1">
      <p class="flex items-center gap-1.5">
        <UIcon name="i-lucide-history" class="size-3.5 shrink-0" aria-hidden="true" />
        <span>
          Letztes Spiel:
          <template v-if="info.lastGameBy">{{ info.lastGameBy }} &middot; </template>
          {{ info.lastGameAt }}
        </span>
      </p>
      <!-- Ohne Wrapper: Der Slot-Inhalt ordnet sich selbst ins Grid ein (z. B. `col-span-2`). -->
      <slot name="lastGameActions" />
    </div>
  </div>
</template>
