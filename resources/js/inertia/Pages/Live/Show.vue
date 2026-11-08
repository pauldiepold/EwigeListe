<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import LiveLayout from '@/Layouts/LiveLayout.vue';
import LiveReadyCheck from '@/Components/Live/LiveReadyCheck.vue';
import LiveSpectators from '@/Components/Live/LiveSpectators.vue';
import LiveTable from '@/Components/Live/LiveTable.vue';
import LiveToolbar from '@/Components/Live/LiveToolbar.vue';
import LiveWaitingForPlayers from '@/Components/Live/LiveWaitingForPlayers.vue';
import { postLive } from '@/composables/liveRequest';
import { useLiveEcho } from '@/composables/useLiveEcho';
import { useLiveGameState } from '@/composables/useLiveGameState';
import { useLiveReadyCheck } from '@/composables/useLiveReadyCheck';
import { useRefreshOnVisible } from '@/composables/useRefreshOnVisible';
import { start as startGame } from '@/routes/live/rounds';
import type { LiveShowProps } from '@/types/live';

const props = defineProps<LiveShowProps>();

const toast = useToast();

/**
 * Der Live-Zustand an einer Stelle: Er startet mit den Props und wird von Pushes (`state.applyPush`)
 * fortgeschrieben. Hand, Stich, Overlays und Meldungen hängen als eigene Komponenten in den Slots
 * des Tisches und lesen von hier.
 */
const state = useLiveGameState(props);

/** Die Spieler des laufenden (bzw. nächsten) Spiels. */
const activePlayers = computed(() => props.round.players.filter((player) => player.isActive));
const activePlayerIds = computed(() => activePlayers.value.map((player) => player.id));
const viewerIsActive = computed(() => activePlayerIds.value.includes(props.round.viewerPlayerId));

const reloading = ref(false);

/** Holt die Props der Seite neu und ersetzt damit den lokalen Zustand (siehe `useLiveGameState`). */
function reloadData(): void {
  router.reload({
    only: ['round', 'liveGame', 'lastLiveGame', 'ich'],
    onStart: () => {
      reloading.value = true;
    },
    onFinish: () => {
      reloading.value = false;
    },
  });
}

useRefreshOnVisible(reloadData);

const readyCheck = useLiveReadyCheck({
  activePlayerIds,
  viewerPlayerId: props.round.viewerPlayerId,
  whisperReady: (playerId) => echo.sendReady(playerId),
  startGame: async () => {
    try {
      await postLive(startGame.url(props.round.liveRoundId));
    } catch (error) {
      toast.add({ title: 'Das Spiel konnte nicht gestartet werden', color: 'error' });
      throw error;
    }
  },
});

const echo = useLiveEcho({
  roundId: props.round.id,
  liveRoundId: props.round.liveRoundId,
  playerId: props.round.viewerPlayerId,
  isActivePlayer: viewerIsActive,
  onPush: state.applyPush,
  onRoundUpdated: reloadData,
  onReady: readyCheck.receiveReady,
  onMemberLeft: readyCheck.reset,
});

const onlinePlayerIds = computed(() => echo.onlineMembers.value.map((member) => member.id));

/**
 * Solange nicht alle Spieler des Spiels online sind, steht der Tisch still. Overlays und Hand
 * der Spielphasen zeigen sich erst, wenn das hier `true` ist.
 */
const allPlayersOnline = computed(() =>
  activePlayerIds.value.every((id) => onlinePlayerIds.value.includes(id)),
);

const spectators = computed(() =>
  echo.onlineMembers.value.filter((member) => !activePlayerIds.value.includes(member.id)),
);

// Jedes neue (oder beendete) Spiel beginnt mit einem frischen Bereit-Check.
watch(() => state.liveGame.value?.id, readyCheck.reset);
</script>

<template>
  <Head title="Live-Spiel" />

  <LiveLayout>
    <!-- Hochformat: Der Tisch braucht die Breite. `portrait:` und `landscape:` folgen der Ausrichtung des Fensters. -->
    <div class="hidden h-full flex-col items-center justify-center gap-4 text-center portrait:flex">
      <UIcon name="i-lucide-smartphone" class="size-12 rotate-90 text-muted" aria-hidden="true" />
      <p class="text-lg font-semibold">Bitte drehe dein Gerät</p>
      <UButton :to="round.path" color="neutral" variant="outline" label="Zurück zur Runde" />
    </div>

    <LiveTable
      class="portrait:hidden"
      :players="round.players"
      :viewer-player-id="round.viewerPlayerId"
      :live-game="state.liveGame.value"
    >
      <template #spectators>
        <LiveSpectators :spectators="spectators" />
      </template>

      <template #tools>
        <LiveToolbar
          :back-path="round.path"
          :rules="round.rules"
          :reloading="reloading"
          @reload="reloadData"
        />
      </template>

      <template #overlay>
        <LiveWaitingForPlayers
          v-if="!allPlayersOnline"
          :players="activePlayers"
          :online-player-ids="onlinePlayerIds"
        />
        <LiveReadyCheck
          v-else-if="state.liveGame.value === null"
          :players="activePlayers"
          :ready-ids="readyCheck.readyIds.value"
          :is-first-game="round.gamesCount === 0"
          :can-be-ready="viewerIsActive"
          :i-am-ready="readyCheck.iAmReady.value"
          @ready="readyCheck.markMeReady"
        />
      </template>
    </LiveTable>
  </LiveLayout>
</template>
