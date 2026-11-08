<script setup lang="ts">
import { computed } from 'vue';
import LivePlayerBadge from '@/Components/Live/LivePlayerBadge.vue';
import { seatAroundViewer } from '@/Components/Live/liveSeating';
import type { LiveGameData, LivePlayer } from '@/types/live';

const props = defineProps<{
  /** Alle Spieler der Runde in Sitzreihenfolge. */
  players: LivePlayer[];
  viewerPlayerId: number;
  /** Das laufende Spiel; `null` vor dem ersten und zwischen zwei Spielen. */
  liveGame: LiveGameData | null;
}>();

defineSlots<{
  /** Zuschauer am Rand oben links. */
  spectators?: () => unknown;
  /** Werkzeugleiste links (letzter Stich, Meldungen, Neu laden, Vollbild, …). */
  tools?: () => unknown;
  /** Platz für das Meldungsprotokoll rechts. */
  messages?: () => unknown;
  /** Der Stich in der Mitte. */
  trick?: () => unknown;
  /** Meine Hand unten. */
  hand?: () => unknown;
  /** Overlays in der Mitte: Warten, Bereit-Check, Vorbehalte, Armut, Wertung. */
  overlay?: () => unknown;
}>();

const POSITIONS = ['unten', 'links', 'oben', 'rechts'] as const;

/** Nach Platz am Tisch, beginnend mit mir unten; Zuschauer sehen die Sitzreihenfolge. */
const seats = computed(() =>
  seatAroundViewer(props.players, props.viewerPlayerId).map((player, index) => ({
    player,
    position: POSITIONS[index],
    display: props.liveGame?.spieler[player.id] ?? null,
    isTurn: props.liveGame?.dran === player.id,
    isVorhand: props.liveGame?.phase === 2 && props.liveGame.vorhand === player.id,
  })),
);

/** Wer im laufenden Spiel aussetzt, spielt nicht mit und sieht nur zu. */
const viewerSitsOut = computed(() => {
  const viewer = props.players.find((player) => player.id === props.viewerPlayerId);

  return props.liveGame !== null && viewer !== undefined && !viewer.isActive;
});

const SEAT_CLASSES: Record<(typeof POSITIONS)[number], string> = {
  unten: 'bottom-2 left-[8%]',
  links: 'top-[24%] left-14',
  oben: 'top-2 left-1/2 -translate-x-1/2',
  rechts: 'top-[24%] right-2',
};
</script>

<template>
  <div class="relative size-full overflow-hidden rounded-xl bg-elevated ring ring-default">
    <div
      v-for="seat in seats"
      :key="seat.player.id"
      class="absolute z-10"
      :class="SEAT_CLASSES[seat.position]"
    >
      <LivePlayerBadge
        :player="seat.player"
        :position="seat.position"
        :display="seat.display"
        :is-turn="seat.isTurn"
        :is-vorhand="seat.isVorhand"
      />
    </div>

    <div class="absolute top-2 left-2 z-20">
      <slot name="spectators" />
    </div>

    <div class="absolute top-1/2 left-2 z-20 -translate-y-1/2">
      <slot name="tools" />
    </div>

    <div class="absolute top-[42%] right-2 z-20">
      <slot name="messages" />
    </div>

    <div class="absolute top-[46%] left-1/2 -translate-x-1/2 -translate-y-1/2">
      <slot name="trick" />
    </div>

    <div class="absolute bottom-0 left-1/2 -translate-x-1/2">
      <slot name="hand" />
    </div>

    <div class="absolute top-1/2 left-1/2 z-30 -translate-x-1/2 -translate-y-1/2">
      <p
        v-if="viewerSitsOut"
        class="rounded-lg bg-default px-4 py-3 text-center font-semibold ring ring-default"
      >
        Du setzt dieses Spiel aus!
      </p>
      <slot name="overlay" />
    </div>
  </div>
</template>
