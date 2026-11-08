<script setup lang="ts">
import {
  POVERTY_ASK_PHASE,
  POVERTY_GIVE_PHASE,
  type PovertyPhase,
} from '@/composables/useLiveGameState';

defineProps<{
  /** 3 die Armut gibt ab, 32 die Frage nach dem Mitnehmen, 33 der Mitnehmer gibt zurück. */
  phase: PovertyPhase;
  /** Ich bin dran: Die Armut (3), der Gefragte (32) oder der Mitnehmer (33). */
  isMyTurn: boolean;
  /** Vorname des Spielers, der dran ist; die anderen sehen, auf wen sie warten. */
  activePlayerName: string;
  /** Genau drei Karten sind gewählt. */
  canSubmit: boolean;
  /** Die Armut hat noch Trumpf auf der Hand. */
  trumpLeft: boolean;
  /** Meine Aktion ist unterwegs; bis zur Antwort lassen sich keine weiteren Buttons drücken. */
  busy?: boolean;
}>();

defineEmits<{
  give: [];
  answer: [take: boolean];
  giveBack: [];
}>();
</script>

<template>
  <div
    class="flex max-w-sm flex-col items-center gap-2 rounded-lg bg-default px-4 py-3 text-center ring ring-default"
  >
    <template v-if="!isMyTurn">
      <p v-if="phase === POVERTY_GIVE_PHASE" class="font-semibold">Die Armut wählt ihre Karten.</p>
      <p v-else-if="phase === POVERTY_ASK_PHASE" class="font-semibold">
        {{ activePlayerName }} überlegt …
      </p>
      <p v-else class="font-semibold">{{ activePlayerName }} gibt Karten zurück …</p>
    </template>

    <template v-else-if="phase === POVERTY_GIVE_PHASE">
      <p class="font-semibold">Wähle aus, welche Karten du abgeben möchtest!</p>
      <p v-if="trumpLeft" class="font-bold text-error">Du musst all deinen Trumpf abgeben!</p>
      <UButton v-if="canSubmit" label="Karten abgeben" :disabled="busy" @click="$emit('give')" />
    </template>

    <template v-else-if="phase === POVERTY_ASK_PHASE">
      <p class="font-semibold">Willst du die Armut mitnehmen?</p>
      <div class="flex gap-3">
        <UButton label="Ja!" :disabled="busy" @click="$emit('answer', true)" />
        <UButton label="Nein!" :disabled="busy" @click="$emit('answer', false)" />
      </div>
    </template>

    <template v-else>
      <p class="font-semibold">Wähle aus, welche Karten du zurückgeben möchtest!</p>
      <UButton
        v-if="canSubmit"
        label="Karten zurückgeben"
        :disabled="busy"
        @click="$emit('giveBack')"
      />
    </template>
  </div>
</template>
