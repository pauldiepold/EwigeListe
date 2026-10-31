<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { store as storeGame } from '@/routes/games';
import type { RoundActivePlayer } from '@/types/round-show';

const MIN_POINTS = -4;
const MAX_POINTS = 16;

const props = defineProps<{
  roundId: number;
  gamesCount: number;
  activePlayers: RoundActivePlayer[];
  viewerIsActive: boolean;
}>();

const toast = useToast();
const open = ref(false);

const form = useForm({
  winners: [] as number[],
  points: 0,
  misplayed: false,
});

/**
 * Die Anordnung entspricht dem Blick vom eigenen Platz: Wer mitspielt, steht unten, der nächste
 * Sitz links, gegenüber oben, der letzte rechts. Wer aussetzt, sitzt gedanklich unten zwischen
 * den vier Aktiven; der nächste aktive Sitz steht dann unten links.
 */
const seatClasses = computed(() =>
  props.viewerIsActive
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

function closeAndReset(): void {
  open.value = false;
  form.reset();
  form.clearErrors();
}

function submit(): void {
  form.post(storeGame.url(props.roundId), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: closeAndReset,
  });
}

/**
 * Ein anderes Handy hat die Runde geändert: Die Auswahl passt nicht mehr, und ein Speichern ergäbe
 * ein doppeltes Spiel oder falsche Punkte. Der Hinweis bleibt stehen, bis er weggeklickt wird.
 */
watch(
  () => props.gamesCount,
  (gamesCount, previousGamesCount) => {
    if (!open.value || form.processing) {
      return;
    }

    closeAndReset();
    toast.add({
      title:
        gamesCount > previousGamesCount
          ? 'Inzwischen wurde ein Spiel eingetragen'
          : 'Inzwischen wurde ein Spiel gelöscht',
      description: 'Prüfe die Tabelle, bevor du ein weiteres Spiel einträgst.',
      color: 'warning',
      icon: 'i-lucide-triangle-alert',
      duration: 0,
    });
  },
);
</script>

<template>
  <UModal
    v-model:open="open"
    title="Welche Personen haben gewonnen?"
    description="Wähle die Gewinner und trage die Punkte ein."
    :ui="{ content: 'top-4 translate-y-0 sm:top-16' }"
  >
    <UButton color="primary" icon="i-lucide-plus" label="Spiel eintragen" />

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
            v-for="(player, seat) in activePlayers"
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

        <UFormField label="Punkte" class="mx-auto w-40">
          <UInputNumber
            v-model="form.points"
            name="points"
            :min="MIN_POINTS"
            :max="MAX_POINTS"
            class="w-full"
          />
        </UFormField>

        <div>
          <UCheckbox v-model="form.misplayed" name="misplayed" label="Falsch bedient?" />
          <p v-if="form.misplayed" class="mt-2 text-sm text-muted">
            Falls jemand falsch bedient, wird dies als verlorenes Solo mit 2 Punkten plus die
            getätigten Ansagen gewertet. Dieses Ergebnis wird oben eingetragen.
          </p>
        </div>
      </form>
    </template>

    <template #footer>
      <div class="flex w-full justify-end gap-3">
        <UButton color="neutral" variant="outline" label="Schließen" @click="closeAndReset" />
        <UButton
          type="submit"
          form="enter-game-form"
          color="primary"
          label="Speichern"
          :disabled="!canSubmit"
          :loading="form.processing"
        />
      </div>
    </template>
  </UModal>
</template>
