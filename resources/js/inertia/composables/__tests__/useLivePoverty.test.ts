import { nextTick } from 'vue';
import { describe, expect, it, vi } from 'vitest';
import { useLiveGameState } from '@/composables/useLiveGameState';
import { useLivePoverty } from '@/composables/useLivePoverty';
import { liveCard, liveGame, liveSpieler } from '@/testing/live';
import type { LiveEngineCard, LiveGameData, LiveSpieler } from '@/types/live';

const VIEWER = 12;

function card(id: number, trumpf = false): LiveEngineCard {
  return liveCard(id, 4, 3, { trumpf });
}

function game(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return liveGame({ id: 5, phase: 3, dran: VIEWER, spieler: {}, ...overrides });
}

function me(hand: LiveEngineCard[]): LiveSpieler {
  return liveSpieler({ id: VIEWER, index: 1, hand, vorbehalt: 'Armut' });
}

/** Eine Hand ohne Trumpf: Karten 1 bis 5 sind frei wählbar. */
const plainHand = [card(1), card(2), card(3), card(4), card(5)];

function setup(liveGame: LiveGameData | null, ich: LiveSpieler | null, send = vi.fn()) {
  send.mockResolvedValue(true);
  const state = useLiveGameState({ liveGame, lastLiveGame: null, ich });
  const poverty = useLivePoverty({
    state,
    viewerPlayerId: VIEWER,
    action: { send },
  });

  return { state, poverty, send };
}

describe('useLivePoverty: Karten zum Abgeben wählen', () => {
  it('wählt Karten aus und legt sie per erneutem Antippen zurück', () => {
    const { poverty } = setup(game(), me(plainHand));

    poverty.toggle(plainHand[0]);
    poverty.toggle(plainHand[2]);
    expect(poverty.selectedIds.value).toEqual([1, 3]);

    poverty.toggle(plainHand[0]);
    expect(poverty.selectedIds.value).toEqual([3]);
  });

  it('lässt nicht mehr als drei Karten zu', () => {
    const { poverty } = setup(game(), me(plainHand));

    for (const c of plainHand) {
      poverty.toggle(c);
    }

    expect(poverty.selectedIds.value).toEqual([1, 2, 3]);
  });

  it('wählt nur, wenn ich die Karten abgeben oder zurückgeben muss', () => {
    const notMyTurn = setup(game({ dran: 11 }), me(plainHand));
    const otherPhase = setup(game({ phase: 4 }), me(plainHand));
    const deciding = setup(game({ phase: 32 }), me(plainHand));
    const spectator = setup(game(), null);

    for (const { poverty } of [notMyTurn, otherPhase, deciding, spectator]) {
      poverty.toggle(plainHand[0]);
      expect(poverty.selecting.value).toBe(false);
      expect(poverty.selectedIds.value).toEqual([]);
    }
  });
});

describe('useLivePoverty: Karten abgeben', () => {
  it('bietet das Abgeben erst bei genau drei gewählten Karten an', () => {
    const { poverty } = setup(game(), me(plainHand));

    for (const c of plainHand.slice(0, 2)) {
      poverty.toggle(c);
    }
    expect(poverty.canSubmit.value).toBe(false);

    poverty.toggle(plainHand[2]);
    expect(poverty.canSubmit.value).toBe(true);

    poverty.toggle(plainHand[2]);
    expect(poverty.canSubmit.value).toBe(false);
  });

  it('schickt die drei Karten an die Engine und leert die Auswahl', async () => {
    const { poverty, send } = setup(game({ id: 5 }), me(plainHand));
    plainHand.slice(0, 3).forEach(poverty.toggle);

    await poverty.give();

    expect(send).toHaveBeenCalledWith('/api/live/5/armutAbgeben', {
      karten: [{ id: 1 }, { id: 2 }, { id: 3 }],
    });
    expect(poverty.selectedIds.value).toEqual([]);
  });

  it('behält die Auswahl, wenn die Engine die Karten ablehnt', async () => {
    const { poverty, send } = setup(game(), me(plainHand));
    send.mockResolvedValue(false);
    plainHand.slice(0, 3).forEach(poverty.toggle);

    await poverty.give();

    expect(poverty.selectedIds.value).toEqual([1, 2, 3]);
  });

  it('verlangt den ganzen Trumpf, bevor etwas gesendet wird', async () => {
    const hand = [card(1, true), card(2), card(3), card(4), card(5, true)];
    const { poverty, send } = setup(game(), me(hand));
    [hand[0], hand[1], hand[2]].forEach(poverty.toggle);

    await poverty.give();

    expect(send).not.toHaveBeenCalled();
    expect(poverty.trumpLeft.value).toBe(true);
    expect(poverty.selectedIds.value).toEqual([1, 2, 3]);
  });

  it('nimmt den Trumpf-Hinweis zurück, sobald die Auswahl sich ändert', async () => {
    const hand = [card(1, true), card(2), card(3), card(4), card(5, true)];
    const { poverty, send } = setup(game(), me(hand));
    [hand[0], hand[1], hand[2]].forEach(poverty.toggle);
    await poverty.give();

    poverty.toggle(hand[2]);
    poverty.toggle(hand[4]);

    expect(poverty.trumpLeft.value).toBe(false);
    await poverty.give();
    expect(send).toHaveBeenCalledWith('/api/live/5/armutAbgeben', {
      karten: [{ id: 1 }, { id: 2 }, { id: 5 }],
    });
  });
});

describe('useLivePoverty: Armut mitnehmen und zurückgeben', () => {
  it('beantwortet die Frage nach dem Mitnehmen mit Ja oder Nein', async () => {
    const { poverty, send } = setup(game({ phase: 32 }), me(plainHand));

    await poverty.answer(true);
    await poverty.answer(false);

    expect(send).toHaveBeenNthCalledWith(1, '/api/live/5/armutMitnehmen', { mitnehmen: true });
    expect(send).toHaveBeenNthCalledWith(2, '/api/live/5/armutMitnehmen', { mitnehmen: false });
  });

  it('gibt als Mitnehmer drei Karten zurück, auch wenn Trumpf dabei ist', async () => {
    const hand = [card(1, true), card(2, true), card(3), card(4), card(5)];
    const { poverty, send } = setup(game({ phase: 33 }), me(hand));
    expect(poverty.selecting.value).toBe(true);
    [hand[0], hand[3], hand[4]].forEach(poverty.toggle);

    await poverty.giveBack();

    expect(send).toHaveBeenCalledWith('/api/live/5/armutZurueck', {
      karten: [{ id: 1 }, { id: 4 }, { id: 5 }],
    });
    expect(poverty.selectedIds.value).toEqual([]);
  });

  it('verwirft die Auswahl, wenn die Phase oder das Spiel wechselt', async () => {
    const { poverty, state } = setup(game(), me(plainHand));
    poverty.toggle(plainHand[0]);

    state.applyPush({ liveGame: game({ phase: 32, dran: 11 }), ich: me(plainHand) });
    await nextTick();
    expect(poverty.selectedIds.value).toEqual([]);

    state.applyPush({ liveGame: game({ phase: 33, dran: VIEWER }), ich: me(plainHand) });
    poverty.toggle(plainHand[1]);
    state.applyPush({ liveGame: game({ id: 6, phase: 33, dran: VIEWER }), ich: me(plainHand) });
    await nextTick();
    expect(poverty.selectedIds.value).toEqual([]);
  });
});

describe('useLivePoverty: erhaltene Karten', () => {
  const received = { ...card(6), armut_zurueck: true };
  const hand = [...plainHand, received, { ...card(7), armut_zurueck: true }];

  it('kennzeichnet beim Zurückgeben die Karten, die ich aus der Armut bekommen habe', () => {
    const { poverty } = setup(game({ phase: 33 }), me(hand));

    expect(poverty.receivedIds.value).toEqual([6, 7]);
  });

  it('kennzeichnet nichts, solange ich keine Armut mitgenommen habe', () => {
    const { poverty } = setup(game({ phase: 3 }), me(hand));

    expect(poverty.receivedIds.value).toEqual([]);
  });

  it('kennzeichnet nichts mehr, sobald das Spiel läuft', () => {
    const { poverty } = setup(game({ phase: 4 }), me(hand));

    expect(poverty.receivedIds.value).toEqual([]);
  });

  it('kennzeichnet nichts für Zuschauer ohne eigenen Spieler', () => {
    const { poverty } = setup(game({ phase: 33 }), null);

    expect(poverty.receivedIds.value).toEqual([]);
  });
});
