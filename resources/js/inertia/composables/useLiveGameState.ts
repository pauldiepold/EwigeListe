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
    if (liveGame.value !== null && push.liveGame.id < liveGame.value.id) {
      return;
    }

    liveGame.value = push.liveGame;

    if (push.ich) {
      ich.value = push.ich;
    }
  }

  return { liveGame, lastLiveGame, ich, hand, applyPush };
}
