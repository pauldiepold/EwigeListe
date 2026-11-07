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

  <AppLayout fill-height>
    <template v-if="canChangeGames && activeTab === 'runde'" #floatingAction>
      <EnterGameModal
        floating
        :round-id="round.id"
        :last-game-id="round.lastGameId"
        :active-players="round.activePlayers"
        :viewer-is-active="round.viewerIsActive"
        @pending="pendingGame = $event"
      />
    </template>

    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col">
      <UTabs v-model="activeTab" :items="tabs" :content="false" class="mb-3 w-full lg:mb-6" />

      <!-- Füllt den Bildschirm, damit die Rundendaten unten stehen und der Footer darunter. -->
      <section v-if="activeTab === 'runde'" class="flex flex-1 flex-col gap-6">
        <!-- Am Desktop ein Button mit Text, darunter schwebt der Plus-Button (siehe `AppLayout`). -->
        <div v-if="canChangeGames" class="hidden justify-center lg:flex">
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
          role="group"
          aria-label="Letztes Spiel"
          class="flex flex-wrap items-center justify-center gap-2"
        >
          <span class="text-sm text-muted" aria-hidden="true">Letztes Spiel</span>
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
            size="sm"
            label="Löschen"
            confirm-label="Wirklich löschen?"
            :loading="deleting"
            @confirm="deleteLastGame"
          />
        </div>

        <UEmpty
          v-if="round.info.gamesCount === 0 && pendingGame !== 'new'"
          class="flex-1"
          variant="naked"
          icon="i-lucide-club"
          title="Noch kein Spiel eingetragen"
        >
          <template v-if="canChangeGames" #description>
            <span class="lg:hidden">Über das + unten rechts trägst du das erste Spiel ein.</span>
            <span class="hidden lg:inline">
              Über „Spiel eintragen“ oben trägst du das erste Spiel ein.
            </span>
          </template>
          <template v-if="round.canUpdate" #actions>
            <ConfirmAction
              label="Runde löschen"
              confirm-label="Runde wirklich löschen?"
              @confirm="deleteRound"
            />
          </template>
        </UEmpty>

        <!--
          So hoch wie der Plus-Button und mit demselben Abstand nach unten: Der Text steht auf
          Höhe seiner Mitte, rechts bleibt Platz für ihn.
        -->
        <RoundInfo
          :info="round.info"
          class="mt-auto mb-[env(safe-area-inset-bottom)] flex min-h-14 flex-col justify-center"
          :class="{ 'pr-20 lg:pr-0': canChangeGames }"
        />
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
