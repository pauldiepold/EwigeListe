import { nextTick, reactive } from 'vue';
import { describe, expect, it } from 'vitest';
import { useLiveGameState } from '@/composables/useLiveGameState';
import type { LiveEngineCard, LiveGameData, LiveShowProps, LiveSpieler } from '@/types/live';

function card(id: number): LiveEngineCard {
  return {
    id,
    farbe: 4,
    wert: 3,
    rang: id,
    punkte: 3,
    trumpf: true,
    spielbar: true,
    gespieltVon: false,
    armut_zurueck: false,
  };
}

function game(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return {
    id: 5,
    phase: 4,
    dran: 11,
    stichNr: 1,
    vorhand: 11,
    aktuellerStich: { karten: [], stecher: null },
    letzterStich: { karten: [], stecher: null },
    spieler: {},
    messages: [],
    spieltyp: 'Normalspiel',
    schweinchen: false,
    kontrasOffengelegt: 0,
    reAnsage: false,
    kontraAnsage: false,
    reAbsage: null,
    kontraAbsage: null,
    ...overrides,
  };
}

function me(hand: LiveSpieler['hand']): LiveSpieler {
  return {
    id: 11,
    index: 0,
    hand,
    armutKarten: [],
    moeglicheVorbehalte: [],
    vorbehalt: null,
    isRe: null,
    ansage: null,
    absage: null,
    moeglicheAnAbsage: null,
    punkte: null,
    parteiOffengelegt: 0,
  };
}

function pageProps(overrides: Partial<LiveShowProps> = {}): LiveShowProps {
  return {
    round: {} as LiveShowProps['round'],
    liveGame: game(),
    lastLiveGame: null,
    ich: me([card(1), card(2)]),
    ...overrides,
  };
}

describe('useLiveGameState', () => {
  it('startet mit dem Stand der Props', () => {
    const state = useLiveGameState(pageProps());

    expect(state.liveGame.value?.id).toBe(5);
    expect(state.hand.value.map((c) => c.id)).toEqual([1, 2]);
  });

  it('übernimmt einen Push von Spieler und Hand ohne Reload', () => {
    const state = useLiveGameState(pageProps());

    state.applyPush({
      liveGame: game({ dran: 12, aktuellerStich: { karten: [card(3)], stecher: null } }),
      ich: me([card(2)]),
    });

    expect(state.liveGame.value?.dran).toBe(12);
    expect(state.liveGame.value?.aktuellerStich.karten).toHaveLength(1);
    expect(state.hand.value.map((c) => c.id)).toEqual([2]);
  });

  it('behält bei einem Zuschauer-Push die fehlende Hand leer', () => {
    const state = useLiveGameState(pageProps({ ich: null }));

    state.applyPush({ liveGame: game({ dran: 13 }) });

    expect(state.liveGame.value?.dran).toBe(13);
    expect(state.ich.value).toBeNull();
    expect(state.hand.value).toEqual([]);
  });

  it('ignoriert einen verspäteten Push eines älteren Spiels', () => {
    // Nach dem Schmeißen speichert der Server das alte Spiel nach dem neuen noch einmal.
    const state = useLiveGameState(pageProps({ liveGame: game({ id: 6, phase: 2 }) }));

    state.applyPush({ liveGame: game({ id: 5, phase: 101 }), ich: me([card(3)]) });

    expect(state.liveGame.value?.id).toBe(6);
    expect(state.hand.value.map((c) => c.id)).toEqual([1, 2]);
  });

  it('liest eine Hand, die als Objekt mit Schlüsseln ankommt', () => {
    const state = useLiveGameState(pageProps({ ich: me({ 0: card(7), 2: card(8) }) }));

    expect(state.hand.value.map((c) => c.id)).toEqual([7, 8]);
  });

  it('ersetzt den Stand, sobald die Seite neue Props bekommt', async () => {
    const props = reactive(pageProps());
    const state = useLiveGameState(props);
    state.applyPush({ liveGame: game({ dran: 12 }), ich: me([card(2)]) });

    props.liveGame = game({ id: 6, phase: 2 });
    props.ich = me([card(9)]);
    await nextTick();

    expect(state.liveGame.value?.id).toBe(6);
    expect(state.hand.value.map((c) => c.id)).toEqual([9]);
  });
});
