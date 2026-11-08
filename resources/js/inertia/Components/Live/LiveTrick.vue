<script setup lang="ts">
import { computed } from 'vue';
import PlayingCard from '@/Components/Live/PlayingCard.vue';
import type { TableSize } from '@/Components/Live/liveHandLayout';
import { seatAroundViewer } from '@/Components/Live/liveSeating';
import { trickLayout, type TrickSeat } from '@/Components/Live/liveTrickLayout';
import type { LiveEngineCard, LivePlayer } from '@/types/live';

const props = defineProps<{
  /** Die Karten des Stichs in der Reihenfolge, in der sie gelegt wurden. */
  cards: LiveEngineCard[];
  /** Alle Spieler der Runde in Sitzreihenfolge. */
  players: LivePlayer[];
  viewerPlayerId: number;
  /** Größe des Tisches in Pixeln; die Karten richten ihre Größe danach aus. */
  tableSize: TableSize;
  /** Spieler-ID des Stich-Gewinners; seine Karte leuchtet. */
  winnerId?: number | null;
}>();

const POSITIONS: TrickSeat[] = ['unten', 'links', 'oben', 'rechts'];

const layout = computed(() => trickLayout(props.tableSize));

/** Jeder Platz mit der Karte, die der Spieler dort gelegt hat (oder leer); spätere Karten liegen oben. */
const slots = computed(() =>
  seatAroundViewer(props.players, props.viewerPlayerId).map((player, index) => {
    const position = POSITIONS[index];
    const order = props.cards.findIndex((card) => card.gespieltVon === player.id);
    const { x, y } = layout.value.offsets[position];

    return {
      player,
      position,
      card: order === -1 ? null : props.cards[order],
      isWinner: order !== -1 && player.id === props.winnerId,
      style: {
        width: `${layout.value.cardWidth}px`,
        transform: `translate(${x}px, ${y}px)`,
        zIndex: order + 1,
      },
    };
  }),
);
</script>

<template>
  <div
    role="group"
    aria-label="Stich"
    class="relative"
    :style="{ width: `${layout.width}px`, height: `${layout.height}px` }"
  >
    <div
      v-for="slot in slots"
      :key="slot.player.id"
      :data-trick-slot="slot.position"
      :data-winner="slot.isWinner || undefined"
      class="absolute top-0 left-0"
      :style="slot.style"
    >
      <PlayingCard :card="slot.card" :winner="slot.isWinner" />
    </div>
  </div>
</template>
