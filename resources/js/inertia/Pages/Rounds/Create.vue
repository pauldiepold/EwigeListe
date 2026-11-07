<script setup lang="ts">
import { computed, nextTick, ref, useTemplateRef } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useSortable } from '@vueuse/integrations/useSortable';
import QuickRegisterModal from '@/Components/Rounds/QuickRegisterModal.vue';
import RoundListSelection from '@/Components/Rounds/RoundListSelection.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useRoundListSelection } from '@/composables/useRoundListSelection';
import { store as storeRound } from '@/routes/rounds';
import type { CreateRoundPlayer } from '@/types/round-create';

const MIN_PLAYERS = 4;
const MAX_PLAYERS = 7;

/** So lange hält man eine Zeile auf dem Touchscreen, bevor sie sich ziehen lässt; kürzer scrollt. */
const TOUCH_HOLD_DELAY_MS = 150;

const props = defineProps<{
  players: CreateRoundPlayer[];
}>();

const authPlayerId = usePage().props.auth.player?.id;

const searchTerm = ref('');
const selectedPlayers = ref<CreateRoundPlayer[]>([]);

const form = useForm({
  players: [] as number[],
  groups: [] as number[],
  liveGame: false,
});

const {
  selectableLists,
  selectedListIds,
  isSelected,
  isLocked,
  setSelected,
  reselectStandardListsOf,
} = useRoundListSelection(selectedPlayers);

const canAddPlayers = computed(() => selectedPlayers.value.length < MAX_PLAYERS);
const canStart = computed(() => selectedPlayers.value.length >= MIN_PLAYERS);

/** Färbt den Hinweis unter dem Button rot, sobald jemand mit zu wenigen Personen starten wollte. */
const hasTriedToStart = ref(false);

const missingPlayersHint = computed(() => {
  const missing = MIN_PLAYERS - selectedPlayers.value.length;
  return missing === 1 ? 'Noch eine Person fehlt.' : `Noch ${missing} Personen fehlen.`;
});

/**
 * Validierungsfehler der Seite. Kein Feld des Formulars hat ein eigenes Eingabefeld
 * (Spieler und Listen werden per Auswahl gesetzt), daher ein Alert für den ersten Fehler.
 */
const validationError = computed(() => Object.values(form.errors)[0]);

const filteredPlayers = computed(() => {
  const query = searchTerm.value.trim().toLowerCase();
  return props.players.filter((player) => {
    const fullName = `${player.surname} ${player.name}`.toLowerCase();
    const notSelected = !selectedPlayers.value.some((entry) => entry.id === player.id);
    return notSelected && fullName.includes(query);
  });
});

function addPlayer(player: CreateRoundPlayer): void {
  if (!canAddPlayers.value || selectedPlayers.value.some((entry) => entry.id === player.id)) {
    return;
  }
  selectedPlayers.value.push(player);
  reselectStandardListsOf(player);
  searchTerm.value = '';
}

/** Spieler aus der Schnellregistrierung; `players` ist nach dem Redirect schon neu geladen. */
function addPlayerById(playerId: number): void {
  const player = props.players.find((entry) => entry.id === playerId);
  if (player) {
    addPlayer(player);
  }
}

function removePlayer(playerId: number): void {
  selectedPlayers.value = selectedPlayers.value.filter((player) => player.id !== playerId);
}

/**
 * Sitzreihenfolge per Ziehen, nur senkrecht: Statt einer frei schwebenden Kopie (ausgeblendet)
 * wandert die Zeile selbst durch die Liste. Am Griff startet das Ziehen sofort, an der übrigen
 * Zeile erst nach kurzem Halten, damit Wischen auf dem Handy weiter scrollt.
 */
const seatList = useTemplateRef<HTMLElement>('seatList');
const seatSortable = useSortable(seatList, selectedPlayers, {
  watchElement: true,
  animation: 150,
  direction: 'vertical',
  forceFallback: true,
  fallbackOnBody: true,
  fallbackClass: 'opacity-0!',
  ghostClass: 'bg-elevated',
  chosenClass: 'bg-elevated',
  delay: TOUCH_HOLD_DELAY_MS,
  delayOnTouchOnly: true,
  // Etwas Zittern beim Halten erlaubt; sonst wird am Handy fast jedes Halten zum Scrollen.
  touchStartThreshold: 5,
  filter: '[data-no-drag]',
  preventOnFilter: false,
});

/** Läuft vor Sortable (Capture-Phase), damit die Verzögerung schon für diesen Druck gilt. */
function chooseDragDelay(event: PointerEvent): void {
  const onHandle = (event.target as HTMLElement | null)?.closest('[data-drag-handle]');
  seatSortable.option('delay', onHandle ? 0 : TOUCH_HOLD_DELAY_MS);
}

/** Ohne Zeiger: Pfeiltasten am Griff. Der Fokus folgt dem Griff an seinen neuen Platz. */
async function movePlayer(fromIndex: number, toIndex: number): Promise<void> {
  if (toIndex < 0 || toIndex >= selectedPlayers.value.length) {
    return;
  }
  const next = [...selectedPlayers.value];
  const [moved] = next.splice(fromIndex, 1);
  next.splice(toIndex, 0, moved);
  selectedPlayers.value = next;

  await nextTick();
  seatList.value?.querySelector<HTMLElement>(`[data-drag-handle="${moved.id}"]`)?.focus();
}

function submitRound(): void {
  hasTriedToStart.value = true;
  if (!canStart.value || form.processing) {
    return;
  }

  form.players = selectedPlayers.value.map((player) => player.id);
  form.groups = selectedListIds.value;
  // Nur bei Fehlern bleiben, wo man ist; die neue Runde öffnet oben.
  form.post(storeRound.url(), { preserveScroll: 'errors' });
}

const initialPlayer = props.players.find((player) => player.id === authPlayerId);
if (initialPlayer) {
  addPlayer(initialPlayer);
}
</script>

<template>
  <Head title="Neue Runde starten" />

  <AppLayout title="Neue Runde starten">
    <div class="mx-auto w-full max-w-md space-y-8">
      <section class="space-y-3">
        <!-- Ein Kasten: Suche oben, darunter die Spieler mit den meisten Spielen zuerst. Die
             angeschnittene fünfte Zeile zeigt, dass die Liste scrollt. -->
        <div
          v-if="canAddPlayers"
          class="overflow-hidden rounded-lg border border-default bg-default"
        >
          <UInput
            v-model="searchTerm"
            icon="i-lucide-search"
            placeholder="Bitte Namen eingeben"
            aria-label="Spieler suchen"
            variant="none"
            size="lg"
            class="w-full border-b border-default"
            @keyup.enter="filteredPlayers.length === 1 ? addPlayer(filteredPlayers[0]) : null"
          />

          <div class="max-h-45 overflow-y-auto py-1">
            <button
              v-for="player in filteredPlayers"
              :key="player.id"
              type="button"
              class="flex w-full cursor-pointer items-center gap-3 px-3 py-1.5 text-left transition hover:bg-elevated"
              @click="addPlayer(player)"
            >
              <UAvatar
                :src="player.avatar_path ?? undefined"
                :alt="`${player.surname} ${player.name}`"
                size="sm"
              />
              <span class="truncate">{{ player.surname }} {{ player.name }}</span>
            </button>
            <p v-if="filteredPlayers.length === 0" class="px-3 py-2 text-sm text-muted">
              Person wurde nicht gefunden.
            </p>
          </div>
        </div>
        <p v-else class="text-center text-sm text-muted">
          Maximal {{ MAX_PLAYERS }} Personen pro Runde.
        </p>

        <div v-if="canAddPlayers" class="flex justify-center">
          <QuickRegisterModal @registered="addPlayerById" />
        </div>
      </section>

      <section v-if="selectedPlayers.length" class="space-y-2">
        <div class="text-center">
          <h2 class="text-muted">
            {{ selectedPlayers.length }}
            {{ selectedPlayers.length === 1 ? 'Person' : 'Personen' }}:
          </h2>
          <p v-if="selectedPlayers.length > 1" class="text-xs text-dimmed">
            Sitzreihenfolge, der Erste gibt zuerst. Zum Ändern ziehen.
          </p>
        </div>

        <ul
          ref="seatList"
          class="divide-y divide-default rounded-lg border border-default bg-default"
          @pointerdown.capture="chooseDragDelay"
        >
          <li
            v-for="(player, index) in selectedPlayers"
            :key="player.id"
            class="flex min-h-12 cursor-grab items-center gap-2 py-1.5 pr-1 pl-1 select-none active:cursor-grabbing"
          >
            <span
              :data-drag-handle="player.id"
              role="button"
              tabindex="0"
              :aria-label="`Platz von ${player.surname} ${player.name} ändern, mit Pfeiltasten`"
              class="grid h-9 w-7 shrink-0 touch-none place-items-center rounded text-dimmed focus-visible:outline-2 focus-visible:outline-primary"
              @keydown.up.prevent="movePlayer(index, index - 1)"
              @keydown.down.prevent="movePlayer(index, index + 1)"
            >
              <UIcon name="i-lucide-grip-vertical" class="size-4" />
            </span>
            <UAvatar
              :src="player.avatar_path ?? undefined"
              :alt="`${player.surname} ${player.name}`"
              size="sm"
            />
            <span class="min-w-0 flex-1 truncate font-medium text-highlighted">
              {{ player.surname }} {{ player.name }}
            </span>
            <!-- Die eigene Person bleibt immer dabei; unsichtbar statt entfernt hält die Zeilen bündig. -->
            <UButton
              data-no-drag
              icon="i-lucide-x"
              size="sm"
              color="neutral"
              variant="ghost"
              :aria-label="`${player.surname} ${player.name} entfernen`"
              :class="{ invisible: player.id === authPlayerId }"
              @click="removePlayer(player.id)"
            />
          </li>
        </ul>
      </section>

      <section class="flex flex-col items-center gap-4">
        <div class="flex items-center gap-3 text-lg">
          <button
            type="button"
            class="cursor-pointer"
            :class="form.liveGame ? 'text-dimmed' : 'font-medium text-highlighted'"
            @click="form.liveGame = false"
          >
            Offline
          </button>
          <USwitch v-model="form.liveGame" color="primary" size="lg" aria-label="Online-Runde" />
          <button
            type="button"
            class="cursor-pointer"
            :class="form.liveGame ? 'font-medium text-highlighted' : 'text-dimmed'"
            @click="form.liveGame = true"
          >
            Online
          </button>
        </div>

        <UAlert
          v-if="validationError"
          color="error"
          variant="subtle"
          icon="i-lucide-circle-alert"
          :title="validationError"
        />

        <div class="flex flex-col items-center gap-2">
          <UButton
            :loading="form.processing"
            size="lg"
            color="primary"
            type="button"
            icon="i-lucide-play"
            label="Neue Runde starten"
            @click="submitRound"
          />
          <!-- Bei genug Personen unsichtbar statt entfernt: Der Platz bleibt, die Höhe springt nicht. -->
          <p
            class="text-center text-xs"
            :class="[hasTriedToStart ? 'text-error' : 'text-dimmed', { invisible: canStart }]"
            :role="hasTriedToStart && !canStart ? 'alert' : undefined"
            :aria-hidden="canStart"
          >
            {{ missingPlayersHint }}
          </p>
        </div>
      </section>

      <RoundListSelection
        v-if="selectedPlayers.length"
        :lists="selectableLists"
        :is-selected="isSelected"
        :is-locked="isLocked"
        :selected-count="selectedListIds.length"
        @toggle="setSelected"
      />
    </div>
  </AppLayout>
</template>
