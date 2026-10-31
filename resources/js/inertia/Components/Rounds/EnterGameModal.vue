<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { store as storeGame, update as updateGame } from '@/routes/games';
import type { RoundActivePlayer, RoundLastGame } from '@/types/round-show';

const MIN_POINTS = -4;
const MAX_POINTS = 16;

const props = defineProps<{
  roundId: number;
  lastGameId: number | null;
  activePlayers: RoundActivePlayer[];
  viewerIsActive: boolean;
  /** Gesetzt, wenn statt eines neuen Spiels dieses letzte Spiel bearbeitet wird. */
  editedGame?: RoundLastGame | null;
}>();

const toast = useToast();
const open = ref(false);

const form = useForm({
  winners: [] as number[],
  points: 0,
  misplayed: false,
});

const isEditing = computed(() => props.editedGame != null);
const players = computed(() => props.editedGame?.activePlayers ?? props.activePlayers);
const isViewerActive = computed(() => props.editedGame?.viewerIsActive ?? props.viewerIsActive);

/**
 * Die Anordnung entspricht dem Blick vom eigenen Platz: Wer mitspielt, steht unten, der nächste
 * Sitz links, gegenüber oben, der letzte rechts. Wer aussetzt, sitzt gedanklich unten zwischen
 * den vier Aktiven; der nächste aktive Sitz steht dann unten links.
 */
const seatClasses = computed(() =>
  isViewerActive.value
    ? ['order-4 col-start-2', 'order-2 col-start-1', 'order-1 col-start-2', 'order-3 col-start-3']
    : ['order-3 col-start-1', 'order-1 col-start-1', 'order-2 col-start-3', 'order-4 col-start-3'],
);

const canSubmit = computed(() => {
  const winnersCount = form.winners.length;

  return form.misplayed ? winnersCount === 3 : winnersCount >= 1 && winnersCount <= 3;
});

const validationError = computed(() => Object.values(form.errors)[0]);

function toggleWinner(playerId: number): void {
  form.winners = form.winners.includes(playerId)
    ? form.winners.filter((winnerId) => winnerId !== playerId)
    : [...form.winners, playerId];
}

/**
 * Auch X, Escape und Klick daneben leeren den Dialog: Eine alte Auswahl passt später oft nicht mehr.
 * Beim Bearbeiten beginnt er mit den Werten des Spiels.
 */
watch(open, (isOpen) => {
  form.clearErrors();

  if (isOpen && props.editedGame) {
    form.defaults({
      winners: [...props.editedGame.winnerIds],
      points: props.editedGame.points,
      misplayed: props.editedGame.isMisplay,
    });
  } else if (!isOpen) {
    form.defaults({ winners: [], points: 0, misplayed: false });
  }

  form.reset();
});

function submit(): void {
  const options = {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      open.value = false;
    },
  };

  if (props.editedGame) {
    form.patch(updateGame.url(props.editedGame.id), options);
  } else {
    form.post(storeGame.url(props.roundId), options);
  }
}

function closeBecauseOfOtherChange(title: string): void {
  if (!open.value || form.processing) {
    return;
  }

  open.value = false;
  toast.add({
    title,
    description: isEditing.value
      ? 'Prüfe die Tabelle, bevor du das Spiel bearbeitest.'
      : 'Prüfe die Tabelle, bevor du ein weiteres Spiel einträgst.',
    color: 'warning',
    icon: 'i-lucide-triangle-alert',
    // Nuxt UI schließt Toasts nicht per Touch außerhalb; daher nach längerer Zeit automatisch.
    duration: 15000,
  });
}

/**
 * Ein anderes Handy hat die Runde geändert: Die Auswahl passt nicht mehr, und ein Speichern ergäbe
 * ein doppeltes Spiel oder falsche Punkte. Der Hinweis schließt sich nach einigen Sekunden von selbst.
 * Spiel-IDs wachsen, ein eingetragenes Spiel hat also immer eine höhere ID als das vorige.
 */
watch(
  () => props.lastGameId,
  (lastGameId, previousLastGameId) => {
    closeBecauseOfOtherChange(
      (lastGameId ?? 0) > (previousLastGameId ?? 0)
        ? 'Inzwischen wurde ein Spiel eingetragen'
        : 'Inzwischen wurde ein Spiel gelöscht',
    );
  },
);

/** Beim Bearbeiten genügt die ID nicht: Ein anderes Handy kann dasselbe Spiel ändern. */
watch(
  () => props.editedGame?.signature,
  (signature, previousSignature) => {
    if (previousSignature !== undefined && signature !== previousSignature) {
      closeBecauseOfOtherChange('Inzwischen wurde das Spiel geändert');
    }
  },
);
</script>

<template>
  <UModal
    v-model:open="open"
    :title="isEditing ? 'Letztes Spiel bearbeiten' : 'Welche Personen haben gewonnen?'"
    description="Wähle die Gewinner und trage die Punkte ein."
    :ui="{ content: 'top-4 translate-y-0 sm:top-16' }"
  >
    <UButton
      v-if="isEditing"
      color="neutral"
      variant="outline"
      icon="i-lucide-pencil"
      label="Letztes Spiel bearbeiten"
    />
    <UButton v-else color="primary" icon="i-lucide-plus" label="Spiel eintragen" />

    <template #body>
      <form id="enter-game-form" class="space-y-6" @submit.prevent="submit">
        <UAlert
          v-if="validationError"
          color="error"
          variant="subtle"
          icon="i-lucide-circle-alert"
          :title="validationError"
        />

        <div class="grid grid-cols-4 gap-3">
          <button
            v-for="(player, seat) in players"
            :key="player.id"
            type="button"
            class="col-span-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border px-2 py-1.5 font-semibold"
            :class="[
              seatClasses[seat],
              form.winners.includes(player.id)
                ? 'border-transparent ring-2 ring-success'
                : 'border-accented',
            ]"
            :aria-pressed="form.winners.includes(player.id)"
            @click="toggleWinner(player.id)"
          >
            <UAvatar :src="player.avatarPath ?? undefined" :alt="player.name" size="sm" />
            {{ player.name }}
          </button>
        </div>

        <div class="flex flex-col items-center gap-1.5 pt-4">
          <div class="flex items-center justify-center gap-2">
            <UButton
              color="neutral"
              variant="outline"
              icon="i-lucide-minus"
              aria-label="Weniger Punkte"
              :disabled="form.points <= MIN_POINTS"
              @mousedown.prevent
              @click="form.points -= 1"
            />
            <UInputNumber
              v-model="form.points"
              name="points"
              :min="MIN_POINTS"
              :max="MAX_POINTS"
              :increment="false"
              :decrement="false"
              class="w-20"
              :ui="{ base: 'text-center' }"
            />
            <UButton
              color="neutral"
              variant="outline"
              icon="i-lucide-plus"
              aria-label="Mehr Punkte"
              :disabled="form.points >= MAX_POINTS"
              @mousedown.prevent
              @click="form.points += 1"
            />
          </div>
          <span class="text-sm font-medium text-highlighted">Punkte</span>
        </div>

        <div class="flex justify-center">
          <UButton
            type="submit"
            color="primary"
            size="lg"
            label="Speichern"
            :disabled="!canSubmit"
            :loading="form.processing"
          />
        </div>

        <div class="flex flex-col items-center pt-4">
          <UCheckbox v-model="form.misplayed" name="misplayed" label="Falsch bedient?" />
          <p v-if="form.misplayed" class="mt-2 text-center text-sm text-muted">
            Falls jemand falsch bedient, wird dies als verlorenes Solo mit 2 Punkten plus die
            getätigten Ansagen gewertet. Dieses Ergebnis wird oben eingetragen.
          </p>
        </div>
      </form>
    </template>

    <template #footer>
      <div class="flex w-full justify-center">
        <UButton color="neutral" variant="link" label="Schließen" @click="open = false" />
      </div>
    </template>
  </UModal>
</template>
