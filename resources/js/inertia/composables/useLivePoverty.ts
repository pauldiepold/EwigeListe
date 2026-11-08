import { computed, ref, watch, type ComputedRef, type Ref } from 'vue';
import type { LiveAction } from '@/composables/useLiveAction';
import {
  POVERTY_GIVE_BACK_PHASE,
  POVERTY_GIVE_PHASE,
  type LiveGameState,
} from '@/composables/useLiveGameState';
import { armutAbgeben, armutMitnehmen, armutZurueckgeben } from '@/routes/live/games';
import type { LiveEngineCard } from '@/types/live';

/** Wie viele Karten die Armut abgibt und der Mitnehmer zurückgibt. */
export const POVERTY_CARD_COUNT = 3;

export type LivePoverty = {
  /** Ich wähle gerade Karten: zum Abgeben (Armut) oder zum Zurückgeben (Mitnehmer). */
  selecting: ComputedRef<boolean>;
  selectedIds: Ref<number[]>;
  toggle: (card: LiveEngineCard) => void;
  /** Genau drei Karten sind gewählt; erst dann erscheint der Button zum Abgeben bzw. Zurückgeben. */
  canSubmit: ComputedRef<boolean>;
  /** Beim Zurückgeben: die Karten auf meiner Hand, die ich aus der Armut mitgenommen habe. */
  receivedIds: ComputedRef<number[]>;
  /** Die Armut wollte abgeben, hat aber noch Trumpf auf der Hand („Du musst all deinen Trumpf abgeben!“). */
  trumpLeft: Ref<boolean>;
  /** Die Armut gibt die gewählten Karten ab. */
  give: () => Promise<void>;
  /** Der Gefragte nimmt die Armut mit (`true`) oder lehnt ab. */
  answer: (take: boolean) => Promise<void>;
  /** Der Mitnehmer gibt die gewählten Karten zurück. */
  giveBack: () => Promise<void>;
};

/**
 * Armut des Live-Spiels: Phase 3 (die Armut gibt drei Karten ab), 32 (reihum die Frage, wer sie
 * mitnimmt) und 33 (der Mitnehmer gibt drei Karten zurück). Die Auswahl liegt hier, weil Hand
 * (hebt die gewählten an) und Overlay (Button, Hinweis) sie gemeinsam brauchen.
 */
export function useLivePoverty(options: {
  state: Pick<LiveGameState, 'liveGame' | 'ich' | 'hand'>;
  viewerPlayerId: number;
  action: Pick<LiveAction, 'send'>;
}): LivePoverty {
  const { state, viewerPlayerId, action } = options;

  const phase = computed(() => state.liveGame.value?.phase ?? null);
  const isMyTurn = computed(() => state.liveGame.value?.dran === viewerPlayerId);
  const selecting = computed(
    () =>
      state.ich.value !== null &&
      isMyTurn.value &&
      (phase.value === POVERTY_GIVE_PHASE || phase.value === POVERTY_GIVE_BACK_PHASE),
  );

  const receivedIds = computed(() =>
    state.ich.value !== null && phase.value === POVERTY_GIVE_BACK_PHASE
      ? state.hand.value.filter((card) => card.armut_zurueck).map((card) => card.id)
      : [],
  );

  const selectedIds = ref<number[]>([]);
  const trumpLeft = ref(false);

  const canSubmit = computed(
    () => selecting.value && selectedIds.value.length === POVERTY_CARD_COUNT,
  );

  // Eine neue Phase oder ein neues Spiel beginnt ohne Auswahl, auch nach einem Reload der Seite.
  watch(
    () => [state.liveGame.value?.id, phase.value],
    () => {
      selectedIds.value = [];
      trumpLeft.value = false;
    },
  );

  function toggle(card: LiveEngineCard): void {
    if (!selecting.value) {
      return;
    }

    trumpLeft.value = false;

    if (selectedIds.value.includes(card.id)) {
      selectedIds.value = selectedIds.value.filter((id) => id !== card.id);
    } else if (selectedIds.value.length < POVERTY_CARD_COUNT) {
      selectedIds.value = [...selectedIds.value, card.id];
    }
  }

  async function sendSelection(url: string): Promise<void> {
    const cards = selectedIds.value.map((id) => ({ id }));

    if (await action.send(url, { karten: cards })) {
      selectedIds.value = [];
    }
  }

  async function give(): Promise<void> {
    const liveGame = state.liveGame.value;

    if (!liveGame || !canSubmit.value || phase.value !== POVERTY_GIVE_PHASE) {
      return;
    }

    trumpLeft.value = state.hand.value.some(
      (card) => card.trumpf && !selectedIds.value.includes(card.id),
    );

    if (!trumpLeft.value) {
      await sendSelection(armutAbgeben.url(liveGame.id));
    }
  }

  async function answer(take: boolean): Promise<void> {
    const liveGame = state.liveGame.value;

    if (liveGame) {
      await action.send(armutMitnehmen.url(liveGame.id), { mitnehmen: take });
    }
  }

  async function giveBack(): Promise<void> {
    const liveGame = state.liveGame.value;

    if (liveGame && canSubmit.value && phase.value === POVERTY_GIVE_BACK_PHASE) {
      await sendSelection(armutZurueckgeben.url(liveGame.id));
    }
  }

  return {
    selecting,
    selectedIds,
    receivedIds,
    toggle,
    canSubmit,
    trumpLeft,
    give,
    answer,
    giveBack,
  };
}
