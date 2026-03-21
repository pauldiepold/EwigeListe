<script setup lang="ts">
import type { HomeRound } from '@/types/home';

defineProps<{
  rounds: HomeRound[];
}>();
</script>

<template>
  <section v-if="rounds.length > 0" class="mb-8">
    <h2 class="mb-4 text-xl font-semibold">Letzte Runden</h2>

    <div class="grid gap-3 sm:grid-cols-2">
      <article v-for="round in rounds" :key="round.id" class="rounded border border-slate-200 bg-white p-3 shadow-sm">
        <h3 class="mb-2 font-semibold text-slate-900">{{ round.players }}</h3>
        <p class="mb-2 text-sm text-slate-600">Letztes Spiel: {{ round.lastGameAt ?? '-' }}</p>
        <a :href="round.path" class="text-sm font-medium text-orange-600 hover:text-orange-700">Runde ansehen</a>
      </article>
    </div>
  </section>
</template>
