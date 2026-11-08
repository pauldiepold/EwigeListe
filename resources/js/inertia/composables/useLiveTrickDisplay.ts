import { computed, onScopeDispose, ref, watch, type ComputedRef, type Ref } from 'vue';
import { GAME_OVER_PHASE, listOf, PLAYING_PHASE } from '@/composables/useLiveGameState';
import type { LiveEngineCard, LiveGameData } from '@/types/live';

/** So lange bleibt ein voller Stich mit hervorgehobenem Gewinner liegen, bevor er abgeräumt wird. */
export const TRICK_CLOSING_MS = 2000;

export type LiveTrickDisplay = {
  /** Der Stich liegt auf dem Tisch (Spielphase) oder der letzte Stich ist eingeblendet. */
  visible: ComputedRef<boolean>;
  /** Die Karten in Spielreihenfolge: der aktuelle Stich oder, solange eingeblendet, der letzte. */
  cards: ComputedRef<LiveEngineCard[]>;
  /** Spieler-ID des Gewinners, solange der letzte Stich zu sehen ist. */
  winnerId: ComputedRef<number | null>;
  /** Der letzte Stich ist zu sehen, weil der Spieler ihn eingeblendet hat oder er gerade abgeräumt wird. */
  showingLastTrick: ComputedRef<boolean>;
  /** Der volle Stich liegt noch kurz auf dem Tisch, bevor er abgeräumt wird. */
  closing: ComputedRef<boolean>;
  /** Der Spieler hat den letzten Stich über die Werkzeugleiste eingeblendet. */
  lastTrickPinned: Ref<boolean>;
  toggleLastTrick: () => void;
  /** Räumt weg, was den aktuellen Stich verdeckt (letzter Stich), z. B. weil ich eine Karte lege. */
  showCurrentTrick: () => void;
};

/**
 * Was in der Mitte des Tisches liegt. Ist ein Stich voll, schickt die Engine ihn als `letzterStich`
 * und leert den aktuellen: Dann bleibt der letzte Stich `TRICK_CLOSING_MS` liegen, der Gewinner leuchtet,
 * danach zeigt der Tisch wieder den aktuellen Stich. Die Werkzeugleiste kann den letzten Stich dauerhaft
 * einblenden.
 *
 * @param liveGame Das laufende Spiel aus dem Live-Zustand, nach dem Spielende das beendete: So bleibt
 *   dessen letzter Stich die 2 Sekunden liegen, bevor der Tisch zur Wertung wechselt.
 * @param currentTrick Die Karten des aktuellen Stichs, inklusive meiner optimistisch gelegten Karte.
 */
export function useLiveTrickDisplay(
  liveGame: Ref<LiveGameData | null>,
  currentTrick: Ref<LiveEngineCard[]>,
): LiveTrickDisplay {
  const closing = ref(false);
  const lastTrickPinned = ref(false);
  let closingTimer: ReturnType<typeof setTimeout> | undefined;

  function stopClosing(): void {
    clearTimeout(closingTimer);
    closing.value = false;
  }

  watch(liveGame, (next, previous) => {
    if (next === null || previous === null || next.id !== previous.id) {
      stopClosing();
      lastTrickPinned.value = false;

      return;
    }

    if (next.phase === GAME_OVER_PHASE) {
      lastTrickPinned.value = false;
    }

    // `stichNr` zählt hoch, sobald die Engine einen vollen Stich verteilt hat.
    if (next.stichNr > previous.stichNr) {
      clearTimeout(closingTimer);
      closing.value = true;
      closingTimer = setTimeout(() => (closing.value = false), TRICK_CLOSING_MS);
    }
  });

  onScopeDispose(() => clearTimeout(closingTimer));

  // Nach dem Spielende gibt es keinen laufenden Stich mehr, den der letzte verdecken könnte.
  const showingLastTrick = computed(
    () => closing.value || (lastTrickPinned.value && liveGame.value?.phase !== GAME_OVER_PHASE),
  );
  const visible = computed(
    () =>
      liveGame.value !== null && (showingLastTrick.value || liveGame.value.phase === PLAYING_PHASE),
  );

  const cards = computed(() =>
    showingLastTrick.value ? listOf(liveGame.value?.letzterStich.karten) : currentTrick.value,
  );

  const winnerId = computed(() =>
    showingLastTrick.value ? (liveGame.value?.letzterStich.stecher ?? null) : null,
  );

  return {
    visible,
    cards,
    winnerId,
    closing: computed(() => closing.value),
    showingLastTrick,
    lastTrickPinned,
    toggleLastTrick: () => (lastTrickPinned.value = !lastTrickPinned.value),
    showCurrentTrick: () => {
      stopClosing();
      lastTrickPinned.value = false;
    },
  };
}
