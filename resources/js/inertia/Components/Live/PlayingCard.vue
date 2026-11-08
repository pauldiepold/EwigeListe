<script setup lang="ts">
import { computed } from 'vue';
import type { LiveCard } from '@/types/live';
import {
  cardFaceUrl,
  cardIndex,
  cardName,
  cardSuitPath,
  isRedCard,
} from '@/Components/Live/playingCards';

const props = withDefaults(
  defineProps<{
    /** Die Karte; ohne Karte erscheint ein leerer Platz in Kartengröße. */
    card: LiveCard | null;
    /** Macht die Karte zum Button. Ohne sie ist sie nur ein Bild (z. B. im Stich). */
    interactive?: boolean;
    /** Nur mit `interactive`: nicht spielbare Karten sind abgedunkelt und gesperrt. */
    playable?: boolean;
    /** Nur mit `interactive`: gesetzt macht es die Karte zum Schalter (Armut), `true` hebt sie an. */
    selected?: boolean;
    /** Die Karte kam aus der Armut: Streifen am oberen Rand und Zusatz im Namen. */
    received?: boolean;
    /** Leuchtrand für die Karte des Stich-Gewinners. */
    winner?: boolean;
  }>(),
  { interactive: false, playable: true, selected: undefined, received: false, winner: false },
);

const emit = defineEmits<{
  press: [card: LiveCard];
}>();

const name = computed(() => {
  if (!props.card) {
    return '';
  }

  return props.received ? `${cardName(props.card)} (aus der Armut erhalten)` : cardName(props.card);
});
const index = computed(() => (props.card ? cardIndex(props.card) : ''));
const color = computed(() =>
  props.card && isRedCard(props.card) ? 'var(--card-red)' : 'var(--card-black)',
);
const isDimmed = computed(() => props.interactive && !props.playable);
const isSelected = computed(() => props.interactive && props.selected === true);

const frameClasses = computed(() => [
  'relative block aspect-[5/7] w-full min-w-0 border-0 bg-transparent p-0 transition-transform duration-200 [container-type:inline-size] motion-reduce:transition-none',
  props.interactive && props.playable ? 'cursor-pointer' : 'cursor-default',
  isSelected.value ? '-translate-y-[20%]' : '',
]);

const FACE_SHADOWS = {
  resting: 'shadow-[0_1px_2px_rgb(0_0_0/.22),0_3px_10px_rgb(0_0_0/.12)]',
  selected: 'shadow-[0_0_0_3px_var(--ui-primary),0_4px_14px_rgb(0_0_0/.25)]',
  winner:
    'shadow-[0_0_0_3px_var(--ui-primary),0_0_22px_6px_color-mix(in_srgb,var(--ui-primary)_55%,transparent)]',
};

const faceClasses = computed(() => [
  'block size-full rounded-[6.4cqw] transition-shadow duration-200 motion-reduce:transition-none dark:[filter:brightness(.88)_sepia(.06)]',
  props.winner
    ? FACE_SHADOWS.winner
    : isSelected.value
      ? FACE_SHADOWS.selected
      : FACE_SHADOWS.resting,
]);

function press() {
  if (props.card) {
    emit('press', props.card);
  }
}
</script>

<template>
  <div
    v-if="!card"
    class="aspect-[5/7] w-full min-w-0 rounded-[6.4%/4.6%] border-2 border-dashed border-(--ui-border-accented)"
    aria-hidden="true"
  />
  <component
    :is="interactive ? 'button' : 'div'"
    v-else
    :type="interactive ? 'button' : undefined"
    :role="interactive ? undefined : 'img'"
    :disabled="isDimmed || undefined"
    :aria-pressed="interactive && selected !== undefined ? selected : undefined"
    :aria-label="name"
    :class="frameClasses"
    class="card-frame"
    @click="interactive ? press() : undefined"
  >
    <svg viewBox="0 0 250 350" aria-hidden="true" focusable="false" :class="faceClasses">
      <rect
        x="1"
        y="1"
        width="248"
        height="348"
        rx="16"
        fill="var(--card-face)"
        stroke="var(--card-edge)"
        stroke-width="2"
      />
      <image :href="cardFaceUrl(card)" x="46" y="58" width="196" height="275" />
      <text
        x="14"
        y="82"
        :fill="color"
        :textLength="index === '10' ? 80 : undefined"
        lengthAdjust="spacingAndGlyphs"
        font-family="'Arial Narrow', 'Roboto Condensed', 'Liberation Sans Narrow', Arial, sans-serif"
        font-stretch="condensed"
        font-size="88"
        font-weight="700"
      >
        {{ index }}
      </text>
      <svg x="12" y="94" width="74" height="74" viewBox="-600 -600 1200 1200">
        <path :d="cardSuitPath(card)" :fill="color" />
      </svg>
      <path
        v-if="received"
        d="M1 17 A16 16 0 0 1 17 1 H233 A16 16 0 0 1 249 17 V19 H1 Z"
        fill="var(--card-received)"
        data-received
      />
      <rect
        width="250"
        height="350"
        rx="16"
        fill="var(--card-shade)"
        class="transition-opacity duration-200"
        :opacity="isDimmed ? 0.4 : 0"
      />
    </svg>
  </component>
</template>

<style scoped>
/*
  Die Kartenfläche bleibt in beiden Modi hell wie echtes Papier und ist deshalb nicht an die
  Nuxt-UI-Farben gebunden; im Dunkelmodus dimmt nur der Filter am SVG (`dark:`-Klasse oben).
*/
.card-frame {
  --card-face: #ffffff;
  --card-edge: #c9d1d9;
  --card-red: #c4122f;
  --card-black: #1b1d22;
  --card-received: #f59e0b;
  --card-shade: #18212c;
}

:global(.dark) .card-frame {
  --card-edge: #0a1016;
  --card-shade: #05090d;
}
</style>
