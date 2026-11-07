<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { store as storeGame, update as updateGame } from '@/routes/games';
import type { RoundActivePlayer, RoundLastGame } from '@/types/round-show';

const MIN_POINTS = -4;
const MAX_POINTS = 16;

/**
 * ± sind die meistgetippten Ziele: rund, gefüllt und größer als normale Buttons. Die Füllung ist
 * eine mittlere Slate-Stufe (hell dunkel, im Dark Mode hell), damit sie hervorsticht, ohne mit dem
 * orangen Speichern zu konkurrieren.
 */
const STEPPER_BUTTON_CLASSES = [
  'size-10 justify-center rounded-full',
  'bg-(--ui-color-neutral-700) hover:bg-(--ui-color-neutral-800) disabled:bg-(--ui-color-neutral-700)',
  'dark:bg-(--ui-color-neutral-400) dark:hover:bg-(--ui-color-neutral-300) dark:disabled:bg-(--ui-color-neutral-400)',
].join(' ');

const props = defineProps<{
  roundId: number;
  lastGameId: number | null;
  activePlayers: RoundActivePlayer[];
  viewerIsActive: boolean;
  /** Gesetzt, wenn statt eines neuen Spiels dieses letzte Spiel bearbeitet wird. */
  editedGame?: RoundLastGame | null;
  /** Runder Plus-Button, der unten rechts über der Seite schwebt, statt eines Buttons mit Text. */
  floating?: boolean;
}>();

const emit = defineEmits<{
  /** Meldet eine ausstehende Antwort, damit die Tabelle ein Skeleton zeigt. */
  pending: [kind: 'new' | 'edited' | null];
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
 *
 * Jeder Platz belegt zwei halbe Zeilen. Links und rechts stehen um eine halbe Zeile versetzt
 * zwischen oben und unten; so ist die Raute nur zwei Plätze hoch statt drei. Ohne eigenen Platz
 * genügen zwei schmale Spalten, sonst klafft in der Mitte eine Lücke.
 */
const seatGridClass = computed(() =>
  isViewerActive.value ? 'max-w-72 grid-cols-3' : 'max-w-56 grid-cols-2',
);

const seatClasses = computed(() =>
  isViewerActive.value
    ? [
        'col-start-2 row-start-3',
        'col-start-1 row-start-2',
        'col-start-2 row-start-1',
        'col-start-3 row-start-2',
      ]
    : [
        'col-start-1 row-start-3',
        'col-start-1 row-start-1',
        'col-start-2 row-start-1',
        'col-start-2 row-start-3',
      ],
);

const canSubmit = computed(() => {
  const winnersCount = form.winners.length;

  return form.misplayed ? winnersCount === 3 : winnersCount >= 1 && winnersCount <= 3;
});

const validationError = computed(() => Object.values(form.errors)[0]);

/** Färbt den Hinweis zur Auswahl rot, sobald jemand mit ungültiger Auswahl speichern wollte. */
const hasTriedToSubmit = ref(false);

/** Steht grau da, solange die Auswahl nicht passt; kurz genug für eine Zeile. */
const winnersHint = computed(() => {
  if (form.misplayed) {
    return 'Wähle genau drei Personen aus.';
  }

  return form.winners.length > 3
    ? 'Höchstens drei Personen können gewinnen.'
    : 'Wähle mindestens eine Person aus.';
});

function toggleWinner(playerId: number): void {
  form.winners = form.winners.includes(playerId)
    ? form.winners.filter((winnerId) => winnerId !== playerId)
    : [...form.winners, playerId];
}

/**
 * Auch Escape und Klick daneben leeren den Dialog: Eine alte Auswahl passt später oft nicht mehr.
 * Beim Bearbeiten beginnt er mit den Werten des Spiels.
 */
watch(open, (isOpen) => {
  if (keepsFormState) {
    return;
  }

  form.clearErrors();
  hasTriedToSubmit.value = false;

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

/**
 * Mittig liegen die häufig getippten Buttons gut erreichbar. Öffnet das Punktefeld am
 * Touch-Gerät die Bildschirmtastatur, rutscht der Dialog nach oben, damit sie Feld und Speichern
 * nicht verdeckt, und bleibt dort bis zum Schließen: Spränge er beim Zuklappen der Tastatur
 * zurück, lägen die Buttons plötzlich woanders als unter dem Finger.
 */
const movedUpForKeyboard = ref(false);

function moveUpForKeyboard(): void {
  if (window.matchMedia('(pointer: coarse)').matches) {
    movedUpForKeyboard.value = true;
  }
}

watch(open, (isOpen) => {
  if (isOpen) {
    return;
  }

  movedUpForKeyboard.value = false;
  // Fokus vor dem Schließen abgeben: Sonst holt die Fokusfalle des Dialogs ihn beim Schließen aufs
  // Punktefeld zurück, und die Tastatur blitzt kurz auf. Ein `blur()` ohne Ziel ignoriert sie.
  if (document.activeElement instanceof HTMLElement) {
    document.activeElement.blur();
  }
});

/** Solange gesetzt, ändern Schließen und Wiederöffnen den Formularstand nicht. */
let keepsFormState = false;

/**
 * Der Dialog schließt sofort beim Speichern; die Tabelle folgt mit der Antwort. Scheitert die
 * Anfrage (Validierung, Netz), öffnet er mit der unveränderten Eingabe und dem Fehler wieder.
 */
function submit(): void {
  hasTriedToSubmit.value = true;
  if (!canSubmit.value) {
    return;
  }

  let succeeded = false;
  const options = {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      succeeded = true;
    },
    onFinish: () => {
      emit('pending', null);
      if (succeeded) {
        keepsFormState = false;
        form.defaults({ winners: [], points: 0, misplayed: false });
        form.reset();
        return;
      }

      open.value = true;
      nextTick(() => {
        keepsFormState = false;
      });
    },
  };

  keepsFormState = true;
  open.value = false;
  emit('pending', isEditing.value ? 'edited' : 'new');

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
  <!-- Ohne X und Footer: Klick daneben und Escape schließen; das spart Platz. Die Beschreibung
       bleibt nur für Screenreader, der Titel sagt genug. -->
  <UModal
    v-model:open="open"
    :title="isEditing ? 'Letztes Spiel bearbeiten' : 'Wer hat gewonnen?'"
    description="Wähle, wer gewonnen hat, und trage die Punkte ein."
    :close="false"
    :ui="{
      content: [
        'divide-y-0 transition-[top,translate] duration-200',
        movedUpForKeyboard ? 'top-4 translate-y-0 sm:top-16' : '',
      ].join(' '),
      header: 'min-h-0 justify-center pt-4 pb-0',
      body: 'pt-8 sm:pt-8',
      wrapper: 'text-center',
      title: 'text-lg font-medium tracking-wide',
      description: 'sr-only',
    }"
  >
    <UButton
      v-if="isEditing"
      color="neutral"
      variant="outline"
      icon="i-lucide-pencil"
      class="px-3"
      :ui="{ leadingIcon: 'size-3.5' }"
      aria-label="Letztes Spiel bearbeiten"
      title="Letztes Spiel bearbeiten"
    />
    <!--
      Rund und ohne Text, damit das Icon mittig sitzt; schwebt unten rechts über der Seite. Beim
      Hovern dunkler statt halb durchsichtig, sonst schiene der Inhalt darunter durch.
    -->
    <UButton
      v-else-if="floating"
      color="primary"
      icon="i-lucide-plus"
      aria-label="Spiel eintragen"
      square
      class="size-14 justify-center rounded-full shadow-[0_6px_18px_hsl(0_0%_0%/0.45)] hover:bg-(--ui-color-primary-600) active:bg-(--ui-color-primary-600) dark:hover:bg-(--ui-color-primary-500) dark:active:bg-(--ui-color-primary-500)"
      :ui="{ leadingIcon: 'size-7' }"
    />
    <UButton v-else color="primary" icon="i-lucide-plus" label="Spiel eintragen" />

    <template #body>
      <form id="enter-game-form" class="space-y-8" @submit.prevent="submit">
        <UAlert
          v-if="validationError"
          color="error"
          variant="subtle"
          icon="i-lucide-circle-alert"
          :title="validationError"
        />

        <!-- Keine Boxen: Gewählt sind Bild mit grünem Ring und Haken-Plakette, in einem gedeckten
             Grün, das im Dark Mode nicht blendet. -->
        <div class="mx-auto grid w-full grid-rows-4 gap-x-2 gap-y-6" :class="seatGridClass">
          <button
            v-for="(player, seat) in players"
            :key="player.id"
            type="button"
            class="row-span-2 flex min-w-0 cursor-pointer flex-col items-center gap-1.5 rounded-lg py-1 focus-visible:outline-2 focus-visible:outline-primary"
            :class="seatClasses[seat]"
            :aria-pressed="form.winners.includes(player.id)"
            @click="toggleWinner(player.id)"
          >
            <span class="relative">
              <UAvatar
                :src="player.avatarPath ?? undefined"
                :alt="player.name"
                size="2xl"
                class="ring-offset-2 ring-offset-(--ui-bg) transition-shadow"
                :class="
                  form.winners.includes(player.id)
                    ? 'ring-2 ring-(--ui-color-success-600) dark:ring-(--ui-color-success-700)'
                    : ''
                "
              />
              <span
                v-if="form.winners.includes(player.id)"
                class="absolute -end-1 -bottom-1 grid size-5 place-items-center rounded-full bg-(--ui-color-success-600) text-white ring-2 ring-(--ui-bg) dark:bg-(--ui-color-success-700)"
              >
                <UIcon name="i-lucide-check" class="size-3.5" />
              </span>
            </span>
            <span class="max-w-full truncate text-sm font-medium text-highlighted">
              {{ player.name }}
            </span>
          </button>
        </div>

        <!-- Etwas mehr Luft über den Punkten als zwischen den übrigen Bereichen. -->
        <div class="flex flex-col items-center gap-1.5 pt-2">
          <div class="flex items-center justify-center gap-6" @focusin="moveUpForKeyboard">
            <UButton
              color="neutral"
              variant="solid"
              icon="i-lucide-minus"
              aria-label="Weniger Punkte"
              :class="STEPPER_BUTTON_CLASSES"
              :ui="{ leadingIcon: 'size-5' }"
              :disabled="form.points <= MIN_POINTS"
              @mousedown.prevent
              @click="form.points -= 1"
            />
            <UInputNumber
              v-model="form.points"
              name="points"
              size="xl"
              :min="MIN_POINTS"
              :max="MAX_POINTS"
              :increment="false"
              :decrement="false"
              class="w-20"
              :ui="{ base: 'text-center text-lg font-semibold' }"
            />
            <UButton
              color="neutral"
              variant="solid"
              icon="i-lucide-plus"
              aria-label="Mehr Punkte"
              :class="STEPPER_BUTTON_CLASSES"
              :ui="{ leadingIcon: 'size-5' }"
              :disabled="form.points >= MAX_POINTS"
              @mousedown.prevent
              @click="form.points += 1"
            />
          </div>
          <span class="text-sm font-medium text-highlighted">Punkte</span>
        </div>

        <div class="flex flex-col items-center gap-2">
          <UButton
            type="submit"
            color="primary"
            size="lg"
            label="Speichern"
            :loading="form.processing"
          />
          <!-- Bei gültiger Auswahl unsichtbar statt entfernt: Der Platz bleibt, die Höhe springt nicht. -->
          <p
            class="text-center text-xs"
            :class="[hasTriedToSubmit ? 'text-error' : 'text-dimmed', { invisible: canSubmit }]"
            :role="hasTriedToSubmit && !canSubmit ? 'alert' : undefined"
            :aria-hidden="canSubmit"
          >
            {{ winnersHint }}
          </p>
        </div>

        <div class="flex flex-col items-center">
          <USwitch v-model="form.misplayed" name="misplayed" label="Falsch bedient" />
          <p v-if="form.misplayed" class="mt-2 text-center text-sm text-muted">
            Zählt als verlorenes Solo: Trage 2 Punkte plus Ansagen ein und wähle die drei, die
            richtig bedient haben.
          </p>
        </div>
      </form>
    </template>
  </UModal>
</template>
