<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import LiveLayout from '@/Layouts/LiveLayout.vue';
import LiveHand from '@/Components/Live/LiveHand.vue';
import LiveMessages from '@/Components/Live/LiveMessages.vue';
import LiveReadyCheck from '@/Components/Live/LiveReadyCheck.vue';
import LiveScoring from '@/Components/Live/LiveScoring.vue';
import LivePoverty from '@/Components/Live/LivePoverty.vue';
import LiveReservations from '@/Components/Live/LiveReservations.vue';
import LiveSpectators from '@/Components/Live/LiveSpectators.vue';
import LiveTable from '@/Components/Live/LiveTable.vue';
import LiveToolbar from '@/Components/Live/LiveToolbar.vue';
import LiveTrick from '@/Components/Live/LiveTrick.vue';
import LiveWaitingForPlayers from '@/Components/Live/LiveWaitingForPlayers.vue';
import { playersOfGame } from '@/Components/Live/liveSeating';
import { postLive } from '@/composables/liveRequest';
import { useLiveAction } from '@/composables/useLiveAction';
import { useLiveEcho } from '@/composables/useLiveEcho';
import { listOf, useLiveGameState } from '@/composables/useLiveGameState';
import { useLivePlay } from '@/composables/useLivePlay';
import { useLivePoverty } from '@/composables/useLivePoverty';
import { useLiveReadyCheck } from '@/composables/useLiveReadyCheck';
import { useRefreshOnVisible } from '@/composables/useRefreshOnVisible';
import { vorbehalt as sendReservation } from '@/routes/live/games';
import { start as startGame } from '@/routes/live/rounds';
import type { LiveEngineCard, LiveShowProps } from '@/types/live';

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

/** Aktionen an die Engine (Vorbehalt, später Karte spielen) mit gemeinsamer Fehlerbehandlung. */
const liveAction = useLiveAction(reloadData);

/** Karte spielen (optimistisch), Stich und An-/Absagen. */
const play = useLivePlay({
  state,
  viewerPlayerId: props.round.viewerPlayerId,
  send: liveAction.send,
});

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

/** Meine Hand liegt auf dem Tisch, sobald ein Spiel läuft und alle da sind. Zuschauer haben keine. */
const showHand = computed(
  () =>
    allPlayersOnline.value &&
    state.liveGame.value !== null &&
    state.ich.value !== null &&
    state.hand.value.length > 0,
);

/** In der Vorbehaltsphase wählen die Spieler reihum, wer dran ist, gibt die Vorhand vor. */
const showReservations = computed(
  () => state.liveGame.value?.phase === 2 && state.ich.value !== null,
);

/** Armut (Phasen 3, 32, 33): Auswahl der Karten für Hand und Overlay (`useLivePoverty`). */
const poverty = useLivePoverty({
  state,
  viewerPlayerId: props.round.viewerPlayerId,
  action: liveAction,
});

/** Ein Tipp auf eine Karte wählt sie für die Armut oder spielt sie. */
function pressCard(card: LiveEngineCard): void {
  if (poverty.selecting.value) {
    poverty.toggle(card);
  } else {
    void play.playCard(card);
  }
}

/** Die Phase der Armut, in der ich mitspiele; sonst `null` (auch für Zuschauer). */
const povertyPhase = computed(() => {
  const phase = state.liveGame.value?.phase;

  return state.ich.value !== null && (phase === 3 || phase === 32 || phase === 33) ? phase : null;
});

const activePlayerName = computed(
  () => props.round.players.find((p) => p.id === state.liveGame.value?.dran)?.firstName ?? '',
);

async function chooseReservation(reservation: string): Promise<void> {
  const liveGame = state.liveGame.value;

  if (liveGame) {
    await liveAction.send(sendReservation.url(liveGame.id), { vorbehalt: reservation });
  }
}

/**
 * Zwischen zwei Spielen (und vor dem ersten) folgt der Bereit-Check, nach einem Spiel zusammen mit
 * dessen Wertung. Das gilt erst, wenn der letzte Stich abgeräumt ist.
 */
const betweenGames = computed(() => state.liveGame.value === null && !play.trick.closing.value);

/** Das beendete Spiel mit seiner Wertung; ein geschmissenes Spiel hat keine. */
const scoredGame = computed(() => {
  const lastGame = state.lastLiveGame.value;

  return lastGame !== null && lastGame.wertungsPunkte != null ? lastGame : null;
});

/** Am Tisch bleibt das beendete Spiel (Anzeigen der Spieler) stehen, solange sein letzter Stich liegt. */
const tableGame = computed(
  () => state.liveGame.value ?? (play.trick.closing.value ? state.lastLiveGame.value : null),
);

/** Die Plätze am Tisch folgen dem angezeigten Spiel, nicht den Spielern, die die Seite schon fürs nächste aktiv setzt. */
const tablePlayers = computed(() => playersOfGame(props.round.players, tableGame.value));

const spectators = computed(() =>
  echo.onlineMembers.value.filter((member) => !activePlayerIds.value.includes(member.id)),
);

// Jedes neue (oder beendete) Spiel beginnt mit einem frischen Bereit-Check.
watch(() => state.liveGame.value?.id, readyCheck.reset);

/** Das Meldungsprotokoll klappt bei neuen Meldungen von selbst auf (`LiveMessages`) und lässt sich per Werkzeugleiste umschalten. */
const messagesOpen = ref(false);
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
      :players="tablePlayers"
      :viewer-player-id="round.viewerPlayerId"
      :live-game="tableGame"
    >
      <template #spectators>
        <LiveSpectators :spectators="spectators" />
      </template>

      <template #tools>
        <LiveToolbar
          v-model:messages-open="messagesOpen"
          v-model:last-trick-open="play.trick.lastTrickPinned.value"
          :back-path="round.path"
          :rules="round.rules"
          :reloading="reloading"
          @reload="reloadData"
        >
          <UButton
            v-if="play.announcement.value"
            :label="play.announcement.value.label"
            size="sm"
            :disabled="play.announcementLocked.value"
            @click="play.announce"
          />
        </LiveToolbar>
      </template>

      <template #messages>
        <LiveMessages
          v-model:open="messagesOpen"
          :messages="state.liveGame.value?.messages ?? []"
          :players="round.players"
        />
      </template>

      <template #trick="{ size }">
        <LiveTrick
          v-if="allPlayersOnline && play.trick.visible.value"
          :cards="play.trick.cards.value"
          :players="tablePlayers"
          :viewer-player-id="round.viewerPlayerId"
          :table-size="size"
          :winner-id="play.trick.winnerId.value"
        />
      </template>

      <template #hand="{ size }">
        <LiveHand
          v-if="showHand"
          :cards="play.hand.value"
          :table-size="size"
          :interactive="poverty.selecting.value || play.handInteractive.value"
          :is-playable="poverty.selecting.value ? undefined : play.isPlayable"
          :selected-ids="poverty.selecting.value ? poverty.selectedIds.value : undefined"
          :received-ids="poverty.receivedIds.value"
          @press="pressCard"
        />
      </template>

      <template #overlay>
        <LiveWaitingForPlayers
          v-if="!allPlayersOnline"
          :players="activePlayers"
          :online-player-ids="onlinePlayerIds"
        />
        <div v-else-if="betweenGames" class="flex items-stretch gap-3">
          <LiveScoring
            v-if="scoredGame"
            :game="scoredGame"
            :players="round.players"
            :viewer-player-id="round.viewerPlayerId"
          />
          <LiveReadyCheck
            :players="activePlayers"
            :ready-ids="readyCheck.readyIds.value"
            :is-first-game="round.gamesCount === 0"
            :can-be-ready="viewerIsActive"
            :i-am-ready="readyCheck.iAmReady.value"
            @ready="readyCheck.markMeReady"
          />
        </div>
        <LiveReservations
          v-else-if="showReservations && state.ich.value"
          :possible="listOf(state.ich.value.moeglicheVorbehalte)"
          :koenigs-solo="round.rules.koenigsSolo"
          :is-my-turn="state.liveGame.value?.dran === round.viewerPlayerId"
          :decided="state.ich.value.vorbehalt != null"
          :busy="liveAction.busy.value"
          @choose="chooseReservation"
        />
        <LivePoverty
          v-else-if="povertyPhase !== null"
          :phase="povertyPhase"
          :is-my-turn="state.liveGame.value?.dran === round.viewerPlayerId"
          :active-player-name="activePlayerName"
          :can-submit="poverty.canSubmit.value"
          :trump-left="poverty.trumpLeft.value"
          :busy="liveAction.busy.value"
          @give="poverty.give"
          @answer="poverty.answer"
          @give-back="poverty.giveBack"
        />
      </template>
    </LiveTable>
  </LiveLayout>
</template>
