import { computed, ref, watch, type ComputedRef, type Ref } from 'vue';
import type {
  LiveEngineCard,
  LiveGameData,
  LiveGamePush,
  LiveShowProps,
  LiveSpieler,
} from '@/types/live';

/** Die Engine liefert Listen mal als Array, mal als Objekt mit Schlüsseln (nach `splice` und `filter`). */
export function listOf<T>(value: T[] | Record<string, T> | null | undefined): T[] {
  return Array.isArray(value) ? value : Object.values(value ?? {});
}

// Die Spielphasen der Engine (`LiveGame::$phase`).

/** Die Spieler melden reihum ihre Vorbehalte. */
export const RESERVATION_PHASE = 2;
/** Die Armut wählt drei Karten zum Abgeben. */
export const POVERTY_GIVE_PHASE = 3;
/** Reihum wird gefragt, wer die Armut mitnimmt. */
export const POVERTY_ASK_PHASE = 32;
/** Der Mitnehmer gibt drei Karten zurück. */
export const POVERTY_GIVE_BACK_PHASE = 33;
/** Das Spiel läuft: Karten werden gespielt. */
export const PLAYING_PHASE = 4;
/** Phase eines beendeten Spiels in der Engine: Die Wertung steht fest. */
export const GAME_OVER_PHASE = 101;

export type PovertyPhase =
  typeof POVERTY_GIVE_PHASE | typeof POVERTY_ASK_PHASE | typeof POVERTY_GIVE_BACK_PHASE;

export type LiveGameState = {
  /** Das laufende Spiel; `null` vor dem ersten und zwischen zwei Spielen. */
  liveGame: Ref<LiveGameData | null>;
  /** Das zuletzt beendete Spiel mit der Wertung. */
  lastLiveGame: Ref<LiveGameData | null>;
  /** Der eigene Spieler mit Hand; `null` für Zuschauer und wer aussetzt. */
  ich: Ref<LiveSpieler | null>;
  hand: ComputedRef<LiveEngineCard[]>;
  /** Übernimmt einen Broadcast (`LiveGameDataBroadcasted` bzw. `...Inaktiv`) direkt in den Zustand. */
  applyPush: (push: LiveGamePush) => void;
};

/**
 * Der Live-Zustand der Seite an einer Stelle. Er startet mit den Props der Seite und wird von
 * Pushes (`applyPush`) direkt fortgeschrieben; liefert ein Reload neue Props, ersetzen sie ihn.
 * Komponenten lesen nur von hier und schreiben nie in die Props.
 */
export function useLiveGameState(
  props: Pick<LiveShowProps, 'liveGame' | 'lastLiveGame' | 'ich'>,
): LiveGameState {
  const liveGame = ref(props.liveGame) as Ref<LiveGameData | null>;
  const lastLiveGame = ref(props.lastLiveGame) as Ref<LiveGameData | null>;
  const ich = ref(props.ich) as Ref<LiveSpieler | null>;

  watch(
    () => [props.liveGame, props.lastLiveGame, props.ich] as const,
    ([nextGame, nextLastGame, nextIch]) => {
      liveGame.value = nextGame;
      lastLiveGame.value = nextLastGame;
      ich.value = nextIch;
    },
  );

  const hand = computed(() => listOf(ich.value?.hand));

  function applyPush(push: LiveGamePush): void {
    // Ein Push des Vorgängerspiels kann nach dem neuen Spiel ankommen (Schmeißen, abgelehnte Armut).
    const knownId = Math.max(liveGame.value?.id ?? 0, lastLiveGame.value?.id ?? 0);

    if (push.liveGame.id < knownId) {
      return;
    }

    // Ein beendetes Spiel ist wie nach einem Reload das letzte Spiel; ein laufendes gibt es nicht mehr.
    if (push.liveGame.phase === GAME_OVER_PHASE) {
      liveGame.value = null;
      lastLiveGame.value = push.liveGame;
      ich.value = null;

      return;
    }

    liveGame.value = push.liveGame;

    if (push.ich) {
      ich.value = push.ich;
    }
  }

  return { liveGame, lastLiveGame, ich, hand, applyPush };
}
