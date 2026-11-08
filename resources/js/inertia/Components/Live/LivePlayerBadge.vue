<script setup lang="ts">
import { computed } from 'vue';
import type { LivePlayer, LivePlayerDisplay } from '@/types/live';

const props = defineProps<{
  player: LivePlayer;
  /** Wo der Spieler sitzt, aus Sicht des Betrachters („unten“, „links“, „oben“, „rechts“). */
  position: string;
  /** Anzeige des Spielers im laufenden Spiel; `null` vor dem Spielbeginn. */
  display: LivePlayerDisplay | null;
  isTurn: boolean;
  /** Vorhand zählt nur in der Vorbehaltsphase und sonst nicht. */
  isVorhand: boolean;
}>();

/** Vorhand, Vorbehalt, Partei, Ansage und Spieltyp, soweit sie schon feststehen. */
const details = computed(() =>
  [
    props.isVorhand ? 'Vorhand' : '',
    props.display?.vorbehalt,
    props.display?.partei,
    props.display?.ansage,
    props.display?.spieltyp,
  ]
    .filter(Boolean)
    .join(' · '),
);
</script>

<template>
  <div
    role="group"
    :aria-label="`${player.firstName}, ${position}`"
    class="flex max-w-48 items-center gap-2 rounded-lg bg-default/90 px-2 py-1.5 text-xs ring transition-shadow"
    :class="isTurn ? 'ring-2 ring-primary' : 'ring-default'"
  >
    <UAvatar :src="player.avatarPath ?? undefined" :alt="player.firstName" size="md" />
    <div class="min-w-0">
      <p class="flex items-center gap-1.5">
        <b class="truncate text-highlighted">{{ player.firstName }}</b>
        <UBadge v-if="isTurn" label="dran" color="primary" size="sm" />
      </p>
      <p v-if="details" class="truncate text-muted">{{ details }}</p>
    </div>
  </div>
</template>
