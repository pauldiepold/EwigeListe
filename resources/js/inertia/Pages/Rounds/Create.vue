<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAppRoute } from '@/composables/useAppRoute';
import type { CreateRoundPlayer } from '@/types/round-create';

const props = defineProps<{
  players: CreateRoundPlayer[];
  loggedInPlayerId: number;
}>();

const page = usePage<{ flash?: { success?: string } }>();
const { route } = useAppRoute();

const searchTerm = ref('');
const isSubmitting = ref(false);
const isLiveGame = ref(false);
const selectedPlayers = ref<CreateRoundPlayer[]>([]);
const selectedGroups = ref<number[]>([1]);
const backendError = ref<string | null>(null);

const flashSuccess = computed(() => page.props.flash?.success as string | undefined);

const filteredPlayers = computed(() => {
  const query = searchTerm.value.trim().toLowerCase();
  return props.players.filter((player) => {
    const fullName = `${player.surname} ${player.name}`.toLowerCase();
    const notSelected = !selectedPlayers.value.some((entry) => entry.id === player.id);
    return notSelected && fullName.includes(query);
  });
});

const selectableGroups = computed(() => {
  const groups: Array<{ id: number; name: string }> = [];
  for (const player of selectedPlayers.value) {
    for (const group of player.groups) {
      if (group.closed === 1) {
        continue;
      }
      if (!groups.some((entry) => entry.id === group.id)) {
        groups.push({ id: group.id, name: group.name });
      }
    }
  }
  return groups;
});

const selectedGroupIds = computed(() => {
  return selectedGroups.value.filter((groupId) => selectableGroups.value.some((group) => group.id === groupId));
});

const groupText = computed(() => {
  if (selectedGroupIds.value.length === 0) {
    return 'keiner Liste';
  }
  if (selectedGroupIds.value.length === 1) {
    return 'einer Liste';
  }
  return `${selectedGroupIds.value.length} Listen`;
});

function ensureDefaultGroups(player: CreateRoundPlayer): void {
  for (const profile of player.profiles) {
    if (profile.default && !selectedGroups.value.includes(profile.group_id)) {
      selectedGroups.value.push(profile.group_id);
    }
  }
}

function addPlayer(player: CreateRoundPlayer): void {
  if (selectedPlayers.value.some((entry) => entry.id === player.id)) {
    return;
  }
  selectedPlayers.value.push(player);
  ensureDefaultGroups(player);
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
  if (selectedPlayers.value.length < 4 || isSubmitting.value) {
    return;
  }

  backendError.value = null;
  isSubmitting.value = true;

  router.post(
    '/rounds',
    {
      players: selectedPlayers.value.map((player) => player.id),
      groups: selectedGroupIds.value,
      liveGame: isLiveGame.value,
    },
    {
      preserveScroll: true,
      onError: (errors) => {
        const first = Object.values(errors)[0];
        backendError.value = typeof first === 'string' ? first : 'Die Runde konnte nicht erstellt werden.';
      },
      onFinish: () => {
        isSubmitting.value = false;
      },
    },
  );
}

const initialPlayer = props.players.find((player) => player.id === props.loggedInPlayerId) ?? props.players[0];
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
          <UFormField label="Suche nach Name" help="Mit Enter hinzufügen, wenn nur ein Treffer bleibt.">
            <UInput
              v-model="searchTerm"
              icon="i-lucide-search"
              placeholder="Bitte Namen eingeben"
              @keyup.enter="filteredPlayers.length === 1 ? addPlayer(filteredPlayers[0]) : null"
            />
          </UFormField>

          <div class="max-h-56 space-y-2 overflow-auto rounded-md border border-default p-2">
            <button
              v-for="player in filteredPlayers"
              :key="player.id"
              type="button"
              class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-left transition hover:bg-muted"
              @click="addPlayer(player)"
            >
              <UAvatar :src="player.avatar_path" :alt="`${player.surname} ${player.name}`" size="sm" />
              <span>{{ player.surname }} {{ player.name }}</span>
            </button>
            <p v-if="filteredPlayers.length === 0" class="px-2 py-1 text-sm text-muted">
              Person wurde nicht gefunden.
            </p>
          </div>

          <UButton
            :to="route('register.quick')"
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
              <UAvatar :src="player.avatar_path" :alt="`${player.surname} ${player.name}`" size="sm" />
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
          v-model="isLiveGame"
          color="primary"
          label="Online-Runde"
          description="Aktiviert die Live-Runden-Funktionen direkt nach dem Start."
        />
      </UCard>

      <div class="space-y-3 text-center">
        <p v-if="selectedPlayers.length" class="text-sm text-muted">Runde wird {{ groupText }} hinzugefügt.</p>
        <UButton
          :loading="isSubmitting"
          :disabled="selectedPlayers.length < 4"
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
