import { computed, onScopeDispose, ref, watch, type ComputedRef, type Ref } from 'vue';
import { ansage as sendAnnouncement, karteSpielen } from '@/routes/live/games';
import { listOf, type LiveGameState } from '@/composables/useLiveGameState';
import { useLiveTrickDisplay, type LiveTrickDisplay } from '@/composables/useLiveTrickDisplay';
import type { LiveEngineCard } from '@/types/live';

/** So lange bleibt der An-/Absage-Button nach dem Tippen gesperrt. */
export const ANNOUNCEMENT_LOCK_MS = 2000;

export type LiveAnnouncement = {
  /** Wert, wie ihn die Engine erwartet (`Re`, `Kontra`, `90`, `60`, `30`, `Schwarz`). */
  value: string | number;
  /** Beschriftung des Buttons. */
  label: string;
};

export type LivePlay = {
  /** Meine Hand ohne die Karte, die gerade unterwegs ist. */
  hand: ComputedRef<LiveEngineCard[]>;
  /** Die Hand ist in der Spielphase antippbar; in den Phasen davor nur zum Anschauen. */
  handInteractive: ComputedRef<boolean>;
  /** Ich bin dran, keine Karte ist unterwegs und die Engine hält die Karte für spielbar. */
  isPlayable: (card: LiveEngineCard) => boolean;
  playCard: (card: LiveEngineCard) => Promise<void>;
  trick: LiveTrickDisplay;
  /** Die mögliche An- oder Absage, sobald eine möglich ist; sonst `null`. */
  announcement: ComputedRef<LiveAnnouncement | null>;
  announcementLocked: Ref<boolean>;
  announce: () => Promise<void>;
};

export type UseLivePlayOptions = {
  state: LiveGameState;
  viewerPlayerId: number;
  /** `useLiveAction(...).send`: Fehlerbehandlung (Toast, Neuladen) inklusive. */
  send: (url: string, data?: Record<string, unknown>) => Promise<boolean>;
};

/**
 * Karten spielen und An-/Absagen im laufenden Spiel.
 *
 * Eine gespielte Karte liegt sofort im Stich (optimistisch) und fehlt in der Hand. Sie bleibt dort, bis
 * der Push des Servers sie aus meiner Hand nimmt. Lehnt der Server ab, kehrt sie in die Hand zurück
 * und `send` meldet den Fehler. Der Zustand der Seite bleibt unberührt.
 */
export function useLivePlay({ state, viewerPlayerId, send }: UseLivePlayOptions): LivePlay {
  const pending = ref<{ gameId: number; card: LiveEngineCard } | null>(null);

  /** Die unterwegs befindliche Karte; endet von selbst, wenn der Push sie aus meiner Hand genommen hat. */
  const pendingCard = computed(() => {
    const current = pending.value;

    if (current === null || state.liveGame.value?.id !== current.gameId) {
      return null;
    }

    return state.hand.value.some((card) => card.id === current.card.id) ? current.card : null;
  });

  watch(pendingCard, (card) => {
    if (card === null) {
      pending.value = null;
    }
  });

  const hand = computed(() => state.hand.value.filter((card) => card.id !== pendingCard.value?.id));

  const isPlaying = computed(() => state.liveGame.value?.phase === 4 && state.ich.value !== null);
  const isMyTurn = computed(() => isPlaying.value && state.liveGame.value?.dran === viewerPlayerId);

  const currentTrick = computed(() => {
    const cards = listOf(state.liveGame.value?.aktuellerStich.karten);

    return pendingCard.value
      ? [...cards, { ...pendingCard.value, gespieltVon: viewerPlayerId, spielbar: false }]
      : cards;
  });

  // Nach dem letzten Stich ist das Spiel beendet (`liveGame` leer); sein letzter Stich liegt noch kurz da.
  const tableGame = computed(() => state.liveGame.value ?? state.lastLiveGame.value);
  const trick = useLiveTrickDisplay(tableGame, currentTrick);

  function isPlayable(card: LiveEngineCard): boolean {
    return isMyTurn.value && pendingCard.value === null && card.spielbar;
  }

  async function playCard(card: LiveEngineCard): Promise<void> {
    const liveGame = state.liveGame.value;

    if (liveGame === null || !isPlayable(card)) {
      return;
    }

    trick.showCurrentTrick();
    pending.value = { gameId: liveGame.id, card };

    if (!(await send(karteSpielen.url(liveGame.id), { karte: card }))) {
      pending.value = null;
    }
  }

  // Auch vor dem eigenen ersten Zug darf man ansagen, wenn der erste Stich noch leer ist.
  const announcement = computed<LiveAnnouncement | null>(() => {
    const liveGame = state.liveGame.value;
    const possible = state.ich.value?.moeglicheAnAbsage;

    if (!liveGame || !isPlaying.value || possible == null || possible === '') {
      return null;
    }

    const isFirstTrickStart =
      liveGame.stichNr === 1 && listOf(liveGame.aktuellerStich.karten).length === 0;

    if (liveGame.dran !== viewerPlayerId && !isFirstTrickStart) {
      return null;
    }

    return {
      value: possible,
      label: /^\d+$/.test(String(possible)) ? `Keine ${possible}` : String(possible),
    };
  });

  const announcementLocked = ref(false);
  let lockTimer: ReturnType<typeof setTimeout> | undefined;

  onScopeDispose(() => clearTimeout(lockTimer));

  async function announce(): Promise<void> {
    const liveGame = state.liveGame.value;

    if (liveGame === null || announcement.value === null || announcementLocked.value) {
      return;
    }

    announcementLocked.value = true;
    clearTimeout(lockTimer);
    lockTimer = setTimeout(() => (announcementLocked.value = false), ANNOUNCEMENT_LOCK_MS);

    await send(sendAnnouncement.url(liveGame.id), { ansage: announcement.value.value });
  }

  return {
    hand,
    handInteractive: isPlaying,
    isPlayable,
    playCard,
    trick,
    announcement,
    announcementLocked,
    announce,
  };
}
