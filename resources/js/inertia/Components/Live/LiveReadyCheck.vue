<script setup lang="ts">
import LivePlayerChecklist from '@/Components/Live/LivePlayerChecklist.vue';
import type { LivePlayer } from '@/types/live';

defineProps<{
  /** Die Spieler des (nächsten) Spiels. */
  players: LivePlayer[];
  readyIds: number[];
  /** Davor wurde noch kein Spiel gespielt. */
  isFirstGame: boolean;
  /** Ich spiele mit und kann „bereit“ melden; wer aussetzt oder zuschaut, sieht nur zu. */
  canBeReady: boolean;
  iAmReady: boolean;
}>();

defineEmits<{ ready: [] }>();
</script>

<template>
  <div class="flex flex-col items-center gap-3 rounded-lg bg-default px-4 py-3 ring ring-default">
    <UButton v-if="canBeReady && !iAmReady" label="Bereit?" @click="$emit('ready')" />
    <p class="text-center font-semibold">
      {{ isFirstGame ? 'Das erste' : 'Das nächste' }} Spiel startet, sobald alle bereit sind
    </p>
    <LivePlayerChecklist
      :players="players"
      :checked-ids="readyIds"
      checked-label="bereit"
      unchecked-label="nicht bereit"
    />
  </div>
</template>
