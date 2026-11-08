import { computed, ref, watch, type ComputedRef, type Ref } from 'vue';

export type LiveReadyCheck = {
  /** IDs der Spieler, die „bereit“ gemeldet haben. */
  readyIds: Ref<number[]>;
  /** Alle Spieler des Spiels sind bereit. */
  allReady: ComputedRef<boolean>;
  iAmReady: ComputedRef<boolean>;
  /** Ein Mitspieler hat „bereit“ geflüstert. */
  receiveReady: (playerId: number) => void;
  /** Ich bin bereit: wird gemerkt. Das Flüstern an die Mitspieler übernimmt der Aufrufer (Echo). */
  markMeReady: () => void;
  /** Jemand hat den Channel verlassen oder ein neues Spiel beginnt: alle müssen neu bestätigen. */
  reset: () => void;
};

type Options = {
  /** Die Spieler des (nächsten) Spiels in Sitzreihenfolge. */
  activePlayerIds: Readonly<Ref<number[]>>;
  viewerPlayerId: number;
  /** Startet das Spiel; wird nur im Browser des ersten Spielers und nur einmal pro Runde gerufen. */
  startGame: () => Promise<void>;
};

/**
 * Der Bereit-Check zwischen zwei Spielen. Jeder Mitspieler flüstert „bereit“ (kein Server-Zustand);
 * sobald alle bereit sind, startet der Browser des ersten Spielers das Spiel. Ein Fehler beim Start
 * setzt den Check zurück, damit alle erneut bestätigen können.
 */
export function useLiveReadyCheck(options: Options): LiveReadyCheck {
  const readyIds = ref<number[]>([]);
  const starterId = computed(() => options.activePlayerIds.value[0]);

  const allReady = computed(
    () =>
      options.activePlayerIds.value.length > 0 &&
      options.activePlayerIds.value.every((id) => readyIds.value.includes(id)),
  );
  const iAmReady = computed(() => readyIds.value.includes(options.viewerPlayerId));

  function receiveReady(playerId: number): void {
    if (!readyIds.value.includes(playerId)) {
      readyIds.value = [...readyIds.value, playerId];
    }
  }

  function markMeReady(): void {
    receiveReady(options.viewerPlayerId);
  }

  function reset(): void {
    readyIds.value = [];
  }

  watch(allReady, async (isReady) => {
    if (!isReady || starterId.value !== options.viewerPlayerId) {
      return;
    }

    try {
      await options.startGame();
    } catch {
      reset();
    }
  });

  return { readyIds, allReady, iAmReady, receiveReady, markMeReady, reset };
}
