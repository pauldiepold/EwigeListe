<script setup lang="ts">
import { computed } from 'vue';
import PlayingCard from '@/Components/Live/PlayingCard.vue';
import { handLayout, type TableSize } from '@/Components/Live/liveHandLayout';
import type { LiveEngineCard } from '@/types/live';

const props = withDefaults(
  defineProps<{
    /** Meine Karten, in der Reihenfolge der Engine (sie sortiert nach Trumpf und Farbe). */
    cards: LiveEngineCard[];
    /** Größe des Tisches in Pixeln; die Hand richtet Kartengröße und Fächer danach aus. */
    tableSize: TableSize;
    /** Die Karten sind Buttons. Ohne sie ist die Hand nur zum Anschauen da (Vorbehaltsphase). */
    interactive?: boolean;
    /** Nur mit `interactive`: nicht spielbare Karten sind abgedunkelt und gesperrt. */
    isPlayable?: (card: LiveEngineCard) => boolean;
    /**
     * Nur mit `interactive`: IDs der ausgewählten Karten (Armut). Ist die Liste gesetzt, sind die
     * Karten Schalter, und ausgewählte rücken in ihrer Achse nach oben.
     */
    selectedIds?: number[];
  }>(),
  { interactive: false, isPlayable: () => true, selectedIds: undefined },
);

defineEmits<{
  press: [card: LiveEngineCard];
}>();

const layout = computed(() => handLayout(props.cards.length, props.tableSize));

/** Alle Karten drehen sich um denselben Punkt, `radius` unter der Oberkante der Karte. */
const cardStyles = computed(() =>
  props.cards.map((_, index) => ({
    width: `${layout.value.cardWidth}px`,
    transformOrigin: `50% ${layout.value.radius}px`,
    transform: `translate(${props.tableSize.width / 2 - layout.value.cardWidth / 2}px, ${layout.value.top}px) rotate(${Math.round(layout.value.angles[index] * 1000) / 1000}deg)`,
    zIndex: index + 1,
  })),
);
</script>

<template>
  <div role="group" aria-label="Deine Hand" class="pointer-events-none absolute inset-0">
    <div
      v-for="(card, index) in cards"
      :key="card.id"
      data-hand-card
      class="pointer-events-auto absolute top-0 left-0 transition-transform duration-200 motion-reduce:transition-none"
      :style="cardStyles[index]"
    >
      <PlayingCard
        :card="card"
        :interactive="interactive"
        :playable="isPlayable(card)"
        :selected="selectedIds ? selectedIds.includes(card.id) : undefined"
        @press="$emit('press', card)"
      />
    </div>
  </div>
</template>
