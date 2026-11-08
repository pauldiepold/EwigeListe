<script setup lang="ts">
import { computed, ref } from 'vue';

const SOLOS = ['Fleischlos', 'Bubensolo', 'Damensolo', 'Königssolo'];
const COLOUR_SOLOS = ['Trumpfsolo', 'Farbsolo Herz', 'Farbsolo Pik', 'Farbsolo Kreuz'];

const props = defineProps<{
  /** Die Vorbehalte, die die Engine mir anbietet (`ich.moeglicheVorbehalte`). */
  possible: string[];
  /** Die Regel „Königssolo“ der Runde; ohne sie bleibt der Button weg. */
  koenigsSolo: boolean;
  isMyTurn: boolean;
  /** Ich habe meinen Vorbehalt schon gemeldet und warte auf die anderen. */
  decided: boolean;
  /** Meine Wahl ist unterwegs; bis zur Antwort lassen sich keine weiteren Buttons drücken. */
  busy?: boolean;
}>();

defineEmits<{
  choose: [reservation: string];
}>();

/** Bei einer Hochzeit ist „Gesund“ keine Option. */
const canReportHealthy = computed(() => !props.possible.includes('Hochzeit'));

const reservations = computed(() =>
  props.possible.filter((name) => !SOLOS.includes(name) && !COLOUR_SOLOS.includes(name)),
);

const solos = computed(() =>
  props.possible.filter(
    (name) => SOLOS.includes(name) && (name !== 'Königssolo' || props.koenigsSolo),
  ),
);

const colourSolos = computed(() => props.possible.filter((name) => COLOUR_SOLOS.includes(name)));

const colourSolosVisible = ref(false);

const locked = computed(() => !props.isMyTurn || props.busy === true);
</script>

<template>
  <div class="rounded-lg bg-default px-4 py-3 ring ring-default">
    <p v-if="decided" class="text-center font-semibold">
      Bitte warte, bis alle ihren Vorbehalt bestimmt haben.
    </p>

    <div v-else class="flex flex-col items-center gap-2">
      <p class="font-semibold">
        {{ isMyTurn ? 'Wähle deinen Vorbehalt:' : 'Mögliche Vorbehalte:' }}
      </p>

      <div class="flex items-start gap-3">
        <div v-if="canReportHealthy" class="flex flex-col gap-1.5">
          <UButton label="Gesund" size="sm" :disabled="locked" @click="$emit('choose', 'Gesund')" />
        </div>

        <div v-if="reservations.length > 0" class="flex flex-col gap-1.5">
          <UButton
            v-for="name in reservations"
            :key="name"
            :label="name"
            size="sm"
            :disabled="locked"
            @click="$emit('choose', name)"
          />
        </div>

        <div v-if="solos.length > 0" class="flex flex-col gap-1.5">
          <UButton
            v-for="name in solos"
            :key="name"
            :label="name"
            size="sm"
            :disabled="locked"
            @click="$emit('choose', name)"
          />
        </div>

        <div v-if="colourSolos.length > 0" class="flex flex-col gap-1.5">
          <UButton
            v-if="!colourSolosVisible"
            label="Farbsolo"
            size="sm"
            @click="colourSolosVisible = true"
          />
          <template v-else>
            <UButton
              v-for="name in colourSolos"
              :key="name"
              :label="name"
              size="sm"
              :disabled="locked"
              @click="$emit('choose', name)"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
