<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import RoundListSelection from '@/Components/Rounds/RoundListSelection.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useRoundListSelection } from '@/composables/useRoundListSelection';
import { quick as quickRegister } from '@/routes/register';
import { store as storeRound } from '@/routes/rounds';
import type { CreateRoundPlayer } from '@/types/round-create';

const MIN_PLAYERS = 4;
const MAX_PLAYERS = 7;

const props = defineProps<{
  players: CreateRoundPlayer[];
  loggedInPlayerId: number;
}>();

const page = usePage<{ flash?: { success?: string } }>();

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

const flashSuccess = computed(() => page.props.flash?.success as string | undefined);
const backendError = computed(() => Object.values(form.errors)[0]);

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

function removePlayer(playerId: number): void {
  selectedPlayers.value = selectedPlayers.value.filter((player) => player.id !== playerId);
}

function movePlayer(fromIndex: number, toIndex: number): void {
  if (toIndex < 0 || toIndex >= selectedPlayers.value.length) {
    return;
  }
  const next = [...selectedPlayers.value];
  const [moved] = next.splice(fromIndex, 1);
  next.splice(toIndex, 0, moved);
  selectedPlayers.value = next;
}

function submitRound(): void {
  if (!canStart.value || form.processing) {
    return;
  }

  form.players = selectedPlayers.value.map((player) => player.id);
  form.groups = selectedListIds.value;
  form.post(storeRound.url(), { preserveScroll: true });
}

const initialPlayer = props.players.find((player) => player.id === props.loggedInPlayerId);
if (initialPlayer) {
  addPlayer(initialPlayer);
}
</script>

<template>
  <Head title="Neue Runde starten" />

  <AppLayout title="Neue Runde starten">
    <div class="mx-auto w-full max-w-2xl space-y-6">
      <UAlert
        v-if="flashSuccess"
        color="success"
        variant="soft"
        icon="i-lucide-check-circle"
        :title="flashSuccess"
      />

      <UAlert
        v-if="backendError"
        color="error"
        variant="soft"
        icon="i-lucide-circle-alert"
        :title="backendError"
      />

      <UCard>
        <template #header>
          <h2 class="text-lg font-semibold">Spieler hinzufügen</h2>
        </template>

        <div class="space-y-4">
          <UAlert
            v-if="!canAddPlayers"
            color="neutral"
            variant="soft"
            icon="i-lucide-users"
            :title="`Maximal ${MAX_PLAYERS} Spieler pro Runde.`"
          />

          <UFormField
            v-if="canAddPlayers"
            label="Suche nach Name"
            help="Mit Enter hinzufügen, wenn nur ein Treffer bleibt."
          >
            <UInput
              v-model="searchTerm"
              icon="i-lucide-search"
              placeholder="Bitte Namen eingeben"
              @keyup.enter="filteredPlayers.length === 1 ? addPlayer(filteredPlayers[0]) : null"
            />
          </UFormField>

          <div
            v-if="canAddPlayers"
            class="max-h-56 space-y-2 overflow-auto rounded-md border border-default p-2"
          >
            <button
              v-for="player in filteredPlayers"
              :key="player.id"
              type="button"
              class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left transition hover:bg-muted"
              @click="addPlayer(player)"
            >
              <UAvatar
                :src="player.avatar_path"
                :alt="`${player.surname} ${player.name}`"
                size="sm"
              />
              <span>{{ player.surname }} {{ player.name }}</span>
            </button>
            <p v-if="filteredPlayers.length === 0" class="px-2 py-1 text-sm text-muted">
              Person wurde nicht gefunden.
            </p>
          </div>

          <UButton
            :to="quickRegister.url()"
            color="secondary"
            variant="outline"
            icon="i-lucide-user-plus"
            label="Neue Person registrieren"
          />
        </div>
      </UCard>

      <UCard v-if="selectedPlayers.length">
        <template #header>
          <h2 class="text-lg font-semibold">
            {{ selectedPlayers.length }} {{ selectedPlayers.length === 1 ? 'Person' : 'Personen' }}
          </h2>
        </template>

        <div class="space-y-2">
          <div
            v-for="(player, index) in selectedPlayers"
            :key="player.id"
            class="flex items-center justify-between rounded-md border border-default px-3 py-2"
          >
            <div class="flex items-center gap-3">
              <UAvatar
                :src="player.avatar_path"
                :alt="`${player.surname} ${player.name}`"
                size="sm"
              />
              <span class="font-medium">{{ player.surname }} {{ player.name }}</span>
            </div>

            <div class="flex items-center gap-2">
              <UButton
                icon="i-lucide-arrow-up"
                size="xs"
                color="neutral"
                variant="ghost"
                :disabled="index === 0"
                @click="movePlayer(index, index - 1)"
              />
              <UButton
                icon="i-lucide-arrow-down"
                size="xs"
                color="neutral"
                variant="ghost"
                :disabled="index === selectedPlayers.length - 1"
                @click="movePlayer(index, index + 1)"
              />
              <UButton
                icon="i-lucide-trash-2"
                size="xs"
                color="error"
                variant="ghost"
                :disabled="player.id === loggedInPlayerId"
                @click="removePlayer(player.id)"
              />
            </div>
          </div>
        </div>
      </UCard>

      <UCard>
        <template #header>
          <h2 class="text-lg font-semibold">Modus</h2>
        </template>

        <USwitch
          v-model="form.liveGame"
          color="primary"
          label="Online-Runde"
          description="Aktiviert die Live-Runden-Funktionen direkt nach dem Start."
        />
      </UCard>

      <RoundListSelection
        v-if="selectedPlayers.length"
        :lists="selectableLists"
        :is-selected="isSelected"
        :is-locked="isLocked"
        :selected-count="selectedListIds.length"
        @toggle="setSelected"
      />

      <div class="space-y-3 text-center">
        <UButton
          :loading="form.processing"
          :disabled="!canStart"
          size="lg"
          color="primary"
          type="button"
          icon="i-lucide-play"
          @click="submitRound"
        >
          Neue Runde starten
        </UButton>
      </div>
    </div>
  </AppLayout>
</template>
