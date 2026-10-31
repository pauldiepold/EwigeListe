<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, WhenVisible } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GamesChartLoading from '@/Components/Groups/GamesChartLoading.vue';
import EnterGameModal from '@/Components/Rounds/EnterGameModal.vue';
import RoundChart from '@/Components/Rounds/RoundChart.vue';
import RoundInfo from '@/Components/Rounds/RoundInfo.vue';
import RoundLists from '@/Components/Rounds/RoundLists.vue';
import RoundTable from '@/Components/Rounds/RoundTable.vue';
import ConfirmAction from '@/Components/Ui/ConfirmAction.vue';
import { useRoundUpdates } from '@/composables/useRoundUpdates';
import { useTabInUrlHash } from '@/composables/useTabInUrlHash';
import { destroy as destroyGame } from '@/routes/games';
import { destroy as destroyRound } from '@/routes/rounds';
import type { RoundShowProps } from '@/types/round-show';

const props = defineProps<RoundShowProps>();

const tabs = computed(() => [
  { label: 'Runde', icon: 'i-lucide-circle-play', value: 'runde' },
  ...(props.round.isLive ? [{ label: 'Live', icon: 'i-lucide-dices', value: 'live' }] : []),
  { label: 'Statistiken', icon: 'i-lucide-chart-area', value: 'statistiken' },
  { label: 'Listen', icon: 'i-lucide-list', value: 'listen' },
]);

const activeTab = useTabInUrlHash(tabs, 'runde', 'Rounds/Show');

const canChangeGames = computed(() => props.round.canUpdate && !props.round.isLive);

/** Ein bereits geladenes Chart lädt mit, ein noch nicht geöffnetes bleibt ungeladen. */
function refresh(): void {
  router.reload({ only: props.roundChart ? ['round', 'roundChart'] : ['round'] });
}

const { onlinePlayerIds } = useRoundUpdates(props.round.id, refresh);

const deleting = ref(false);
const pendingGame = ref<'new' | 'edited' | null>(null);

function deleteLastGame(): void {
  if (props.round.lastGameId === null) {
    return;
  }

  router.delete(destroyGame.url(props.round.lastGameId), {
    preserveState: true,
    preserveScroll: true,
    onStart: () => {
      deleting.value = true;
    },
    onFinish: () => {
      deleting.value = false;
    },
  });
}

function deleteRound(): void {
  router.delete(destroyRound.url(props.round.id));
}
</script>

<template>
  <Head title="Rundenübersicht" />

  <AppLayout title="Rundenübersicht">
    <div class="mx-auto w-full max-w-5xl space-y-6">
      <UTabs v-model="activeTab" :items="tabs" :content="false" class="w-full" />

      <section v-if="activeTab === 'runde'" class="space-y-6">
        <div v-if="canChangeGames" class="flex justify-center">
          <EnterGameModal
            :round-id="round.id"
            :last-game-id="round.lastGameId"
            :active-players="round.activePlayers"
            :viewer-is-active="round.viewerIsActive"
            @pending="pendingGame = $event"
          />
        </div>

        <RoundTable
          :players="round.players"
          :games="round.games"
          :online-player-ids="onlinePlayerIds"
          :pending-game="pendingGame"
        />

        <div
          v-if="canChangeGames && round.lastGameId !== null"
          class="flex flex-wrap items-center justify-center gap-3"
        >
          <EnterGameModal
            :round-id="round.id"
            :last-game-id="round.lastGameId"
            :active-players="round.activePlayers"
            :viewer-is-active="round.viewerIsActive"
            :edited-game="round.lastGame"
            @pending="pendingGame = $event"
          />
          <!-- Der `key` bricht eine Bestätigung ab, sobald ein anderes Spiel das letzte ist. -->
          <ConfirmAction
            :key="round.lastGameId"
            icon="i-lucide-trash-2"
            label="Letztes Spiel löschen"
            confirm-label="Letztes Spiel wirklich löschen?"
            :loading="deleting"
            @confirm="deleteLastGame"
          />
        </div>

        <RoundInfo :info="round.info" />

        <div v-if="round.canUpdate && round.info.gamesCount === 0" class="flex justify-center">
          <ConfirmAction
            label="Runde löschen"
            confirm-label="Runde wirklich löschen?"
            @confirm="deleteRound"
          />
        </div>
      </section>

      <UAlert
        v-else-if="activeTab === 'live'"
        class="mx-auto max-w-xl"
        color="neutral"
        variant="subtle"
        icon="i-lucide-dices"
        title="Das Live-Spiel ist hier gerade nicht verfügbar."
      />

      <section v-else-if="activeTab === 'statistiken'">
        <WhenVisible v-if="round.hasChart" data="roundChart">
          <template #fallback>
            <GamesChartLoading />
          </template>

          <RoundChart v-if="roundChart" :chart-data="roundChart" />
        </WhenVisible>
        <p v-else class="text-center text-muted">Ab 4 Spielen verfügbar.</p>
      </section>

      <RoundLists
        v-else-if="activeTab === 'listen'"
        :round-id="round.id"
        :lists="round.lists"
        :can-update="round.canUpdate"
      />
    </div>
  </AppLayout>
</template>
