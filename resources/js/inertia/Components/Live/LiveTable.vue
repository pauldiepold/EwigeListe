<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import LivePlayerBadge from '@/Components/Live/LivePlayerBadge.vue';
import type { TableSize } from '@/Components/Live/liveHandLayout';
import { seatAroundViewer, TABLE_SEATS } from '@/Components/Live/liveSeating';
import type { TrickSeat } from '@/Components/Live/liveTrickLayout';
import { RESERVATION_PHASE } from '@/composables/useLiveGameState';
import type { LiveGameData, LivePlayer } from '@/types/live';

const props = defineProps<{
  /** Alle Spieler der Runde in Sitzreihenfolge. */
  players: LivePlayer[];
  viewerPlayerId: number;
  /** Spieler, die gerade am Tisch sind; ihr Avatar leuchtet grün. */
  onlinePlayerIds: number[];
  /** Das laufende Spiel; `null` vor dem ersten und zwischen zwei Spielen. */
  liveGame: LiveGameData | null;
}>();

defineSlots<{
  /** Zuschauer am Rand oben rechts. */
  spectators?: () => unknown;
  /** Werkzeugleiste oben links (letzter Stich, Meldungen, Neu laden, Vollbild, …). */
  tools?: () => unknown;
  /** Platz für das Meldungsprotokoll rechts. */
  messages?: () => unknown;
  /** Der Stich in der Mitte. `size` ist die Größe des Tisches in Pixeln, nach der sich Karten richten. */
  trick?: (props: { size: TableSize }) => unknown;
  /** Meine Hand unten, in Tischkoordinaten (`absolute inset-0`). `size` ist die Größe des Tisches in Pixeln. */
  hand?: (props: { size: TableSize }) => unknown;
  /** Overlays oben mittig, knapp unter dem oberen Spieler: Warten, Bereit-Check, Vorbehalte, Armut, Wertung. */
  overlay?: () => unknown;
}>();

const tableElement = ref<HTMLElement | null>(null);

/** Größe des Tisches in Pixeln. Karten in Hand und Stich richten ihre Größe danach aus. */
const size = ref<TableSize>({ width: 0, height: 0 });

let resizeObserver: ResizeObserver | undefined;

function measure(): void {
  if (tableElement.value) {
    size.value = {
      width: tableElement.value.clientWidth,
      height: tableElement.value.clientHeight,
    };
  }
}

onMounted(() => {
  measure();

  if (typeof ResizeObserver !== 'undefined' && tableElement.value) {
    resizeObserver = new ResizeObserver(measure);
    resizeObserver.observe(tableElement.value);
  }
});

onBeforeUnmount(() => resizeObserver?.disconnect());

/** Nach Platz am Tisch, beginnend mit mir unten; Zuschauer sehen die Sitzreihenfolge. */
const seats = computed(() =>
  seatAroundViewer(props.players, props.viewerPlayerId).map((player, index) => ({
    player,
    position: TABLE_SEATS[index],
    display: props.liveGame?.spieler[player.id] ?? null,
    isTurn: props.liveGame?.dran === player.id,
    isOnline: props.onlinePlayerIds.includes(player.id),
    isVorhand: props.liveGame?.phase === RESERVATION_PHASE && props.liveGame.vorhand === player.id,
  })),
);

/** Wer im laufenden Spiel aussetzt, spielt nicht mit und sieht nur zu. */
const viewerSitsOut = computed(() => {
  const viewer = props.players.find((player) => player.id === props.viewerPlayerId);

  return props.liveGame !== null && viewer !== undefined && !viewer.isActive;
});

/** Links oben sitzt die Werkzeugleiste; mein Badge steht ganz links unten, weg von der Hand. */
const SEAT_CLASSES: Record<TrickSeat, string> = {
  unten: 'bottom-2 left-2',
  links: 'top-[24%] left-16',
  oben: 'top-2 left-1/2 -translate-x-1/2',
  rechts: 'top-[24%] right-2',
};
</script>

<template>
  <div
    ref="tableElement"
    class="relative size-full overflow-hidden rounded-xl bg-elevated ring ring-default"
  >
    <!-- Die Badges liegen über den äußeren Karten der Hand und lassen Klicks auf diese durch. -->
    <div
      v-for="seat in seats"
      :key="seat.player.id"
      class="pointer-events-none absolute z-10"
      :class="SEAT_CLASSES[seat.position]"
    >
      <LivePlayerBadge
        :player="seat.player"
        :position="seat.position"
        :display="seat.display"
        :is-turn="seat.isTurn"
        :is-online="seat.isOnline"
        :is-vorhand="seat.isVorhand"
      />
    </div>

    <div class="absolute top-2 right-2 z-20">
      <slot name="spectators" />
    </div>

    <div class="absolute top-2 left-2 z-20">
      <slot name="tools" />
    </div>

    <div class="absolute top-[42%] right-2 z-20">
      <slot name="messages" />
    </div>

    <div class="absolute top-[40%] left-1/2 -translate-x-1/2 -translate-y-1/2">
      <slot name="trick" :size="size" />
    </div>

    <div class="pointer-events-none absolute inset-0">
      <slot name="hand" :size="size" />
    </div>

    <div class="absolute top-18 left-1/2 z-30 -translate-x-1/2">
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
