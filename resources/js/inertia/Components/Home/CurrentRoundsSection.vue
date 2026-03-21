<script setup lang="ts">
import type { HomeRound } from '@/types/home';

defineProps<{
  rounds: HomeRound[];
}>();
</script>

<template>
  <section v-if="rounds.length > 0" class="mb-8">
    <h2 class="mb-4 text-xl font-semibold text-highlighted">Letzte Runden</h2>

    <div class="grid gap-3 sm:grid-cols-2">
      <article
        v-for="round in rounds"
        :key="round.id"
        class="rounded border border-default bg-elevated p-3 shadow-sm"
      >
        <h3 class="mb-2 font-semibold text-highlighted">{{ round.players }}</h3>
        <p class="mb-2 text-sm text-muted">Letztes Spiel: {{ round.lastGameAt ?? '-' }}</p>
        <a :href="round.path" class="text-sm font-medium text-primary hover:underline">Runde ansehen</a>
      </article>
    </div>
  </section>
</template>
