<script setup lang="ts">
import { computed } from 'vue';
import type { LiveGameData, LivePlayer } from '@/types/live';

const props = defineProps<{
  /** Das beendete Spiel mit seiner Wertung. */
  game: LiveGameData;
  /** Alle Spieler der Runde. */
  players: LivePlayer[];
  viewerPlayerId: number;
}>();

const winnerIds = computed(() => props.game.winners ?? []);
const winners = computed(() =>
  props.players.filter((player) => winnerIds.value.includes(player.id)),
);

/** Wer im Spiel mitgespielt hat, erfährt Sieg oder Niederlage; Zuschauer und wer aussetzt nicht. */
const viewerPlayed = computed(() => props.viewerPlayerId in props.game.spieler);
const viewerWon = computed(() => winnerIds.value.includes(props.viewerPlayerId));

const points = computed(() => props.game.wertungsPunkte ?? 0);
const pointsLabel = computed(() => (points.value > 0 ? `+${points.value}` : String(points.value)));
</script>

<template>
  <div class="flex flex-col gap-2 rounded-lg bg-default px-4 py-3 ring ring-default">
    <p v-if="viewerPlayed" class="text-center font-bold">
      {{ viewerWon ? 'Du hast gewonnen!' : 'Du hast verloren!' }}
    </p>

    <div class="flex justify-around gap-4 text-sm">
      <span>Re: {{ game.reAugen }}</span>
      <span>Kontra: {{ game.kontraAugen }}</span>
    </div>

    <table class="w-full text-sm" aria-label="Wertung">
      <tbody>
        <tr v-for="([label, value], index) in game.wertung ?? []" :key="index">
          <td class="py-0.5 text-left">{{ label }}</td>
          <td class="py-0.5 text-right">{{ value }}</td>
        </tr>
        <tr class="border-t border-default font-semibold">
          <td></td>
          <td class="py-0.5 text-right">{{ pointsLabel }}</td>
        </tr>
      </tbody>
    </table>

    <ul aria-label="Gewinner" class="flex justify-around gap-3">
      <li
        v-for="winner in winners"
        :key="winner.id"
        :aria-label="winner.firstName"
        class="flex items-center gap-2 text-sm"
      >
        <UAvatar :src="winner.avatarPath ?? undefined" :alt="winner.firstName" size="sm" />
        <span>{{ winner.firstName }}</span>
      </li>
    </ul>

    <p class="text-center font-semibold">
      {{ game.gewinntRe ? 'Re' : 'Kontra' }} gewinnt mit {{ points }}
      {{ points === 1 ? 'Punkt' : 'Punkten' }}!
    </p>
  </div>
</template>
