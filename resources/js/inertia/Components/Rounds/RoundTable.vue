<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { RoundGame, RoundPlayer } from '@/types/round-show';

const HIGHLIGHT_MILLISECONDS = 2000;

/** Erst ab 6 Spielern setzen mehrere aus, dann lohnt die Markierung der Aktiven. */
const MARK_ACTIVE_FROM_PLAYERS = 6;

const props = defineProps<{
  players: RoundPlayer[];
  games: RoundGame[];
  onlinePlayerIds: number[];
}>();

const marksActivePlayers = computed(() => props.players.length >= MARK_ACTIVE_FROM_PLAYERS);

const highlightedGameIds = ref<number[]>([]);
const highlightTimers = new Set<ReturnType<typeof setTimeout>>();

/** Beim ersten Laden leuchtet nichts, danach jede neu erschienene Zeile. */
watch(
  () => props.games.map((game) => game.id),
  (gameIds, previousGameIds) => {
    const newGameIds = gameIds.filter((gameId) => !previousGameIds.includes(gameId));
    if (newGameIds.length === 0) {
      return;
    }

    highlightedGameIds.value = [...highlightedGameIds.value, ...newGameIds];
    const timer = setTimeout(() => {
      highlightTimers.delete(timer);
      highlightedGameIds.value = highlightedGameIds.value.filter(
        (gameId) => !newGameIds.includes(gameId),
      );
    }, HIGHLIGHT_MILLISECONDS);
    highlightTimers.add(timer);
  },
);

onUnmounted(() => {
  highlightTimers.forEach(clearTimeout);
});

function rowBackground(game: RoundGame): string {
  if (highlightedGameIds.value.includes(game.id)) {
    return 'bg-success/30';
  }
  if (game.isMisplay) {
    return 'bg-error/15';
  }
  if (game.isSolo) {
    return 'bg-elevated';
  }
  return '';
}
</script>

<template>
  <div class="mx-auto max-w-3xl overflow-x-auto">
    <table class="w-full text-center tabular-nums">
      <thead>
        <tr class="border-b-2 border-(--ui-color-neutral-400) dark:border-(--ui-color-neutral-600)">
          <th v-for="player in players" :key="player.id" class="px-1 py-2 font-normal">
            <component
              :is="player.path ? Link : 'span'"
              :href="player.path ?? undefined"
              class="flex flex-col items-center gap-1"
            >
              <UAvatar
                :src="player.avatarPath ?? undefined"
                :alt="player.surname"
                :class="{ 'ring-2 ring-success': onlinePlayerIds.includes(player.id) }"
              />
              <span
                class="rounded-lg px-1.5 py-0.5 font-semibold"
                :class="[
                  player.isDealer ? 'text-primary' : 'text-highlighted',
                  { 'bg-primary/15': marksActivePlayers && player.isActive },
                ]"
              >
                {{ player.surname }}
              </span>
            </component>
          </th>
          <th class="px-1 py-2 align-bottom font-semibold text-highlighted">
            <span class="px-1.5 py-0.5">Punkte</span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="game in games"
          :key="game.id"
          class="transition-colors duration-1000"
          :class="[
            rowBackground(game),
            game.endsDealerCycle
              ? 'border-b-2 border-(--ui-color-neutral-400) dark:border-(--ui-color-neutral-600)'
              : 'border-b border-accented/70',
          ]"
        >
          <td
            v-for="(score, seat) in game.scores"
            :key="seat"
            class="px-1 py-1.5"
            :class="{ 'font-bold': score.won }"
          >
            {{ score.points ?? '-' }}
          </td>
          <td class="px-1 py-1.5">{{ game.points }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
