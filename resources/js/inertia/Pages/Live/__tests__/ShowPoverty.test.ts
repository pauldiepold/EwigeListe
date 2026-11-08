import { type Ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { screen, waitFor } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Show from '@/Pages/Live/Show.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveEchoMember, UseLiveEchoOptions } from '@/composables/useLiveEcho';
import type {
  LiveCardRank,
  LiveCardSuit,
  LiveEngineCard,
  LiveGameData,
  LivePlayer,
  LiveShowProps,
  LiveSpieler,
} from '@/types/live';

/** Ersetzt die Echo-Anbindung: Der Test löst Pushes aus und stellt die Online-Liste ein. */
const echo = vi.hoisted(() => ({
  options: null as UseLiveEchoOptions | null,
  onlineMembers: null as Ref<LiveEchoMember[]> | null,
}));

vi.mock('@/composables/useLiveEcho', async () => {
  const { ref } = await import('vue');
  echo.onlineMembers = ref([]);

  return {
    useLiveEcho: (options: UseLiveEchoOptions) => {
      echo.options = options;

      return { onlineMembers: echo.onlineMembers, sendReady: vi.fn() };
    },
  };
});

const postLive = vi.hoisted(() => vi.fn());
vi.mock('@/composables/liveRequest', () => ({ postLive }));

// `Head` und Links brauchen die App, die nur `createInertiaApp` anlegt.
vi.mock('@inertiajs/vue3', async (importOriginal) => {
  const { defineComponent } = await import('vue');

  return {
    ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
    Head: defineComponent({ render: () => null }),
    usePage: () => ({ url: '/runde/7/live', props: {} }),
  };
});

const rules = {
  schweinchen: true,
  fuchsSticht: true,
  schweinchenTrumpfsolo: false,
  koenigsSolo: true,
  karlchen: true,
  karlchenFangen: false,
};

function player(seat: number, name: string): LivePlayer {
  return {
    id: 10 + seat,
    firstName: name,
    lastName: 'Test',
    fullName: `${name} Test`,
    avatarPath: null,
    isActive: true,
  };
}

/** Bernd (11) bis Eva (14) spielen; Clara (12) hat die Armut, Dieter (13) wird gefragt. */
const players = [player(1, 'Bernd'), player(2, 'Clara'), player(3, 'Dieter'), player(4, 'Eva')];
const members: LiveEchoMember[] = players.map((p) => ({
  id: p.id,
  name: p.fullName,
  avatar_path: null,
}));

function card(id: number, farbe: LiveCardSuit, wert: LiveCardRank, trumpf = false): LiveEngineCard {
  return {
    id,
    farbe,
    wert,
    rang: id,
    punkte: 0,
    trumpf,
    spielbar: true,
    gespieltVon: false,
    armut_zurueck: false,
  };
}

const pikNeun = card(1, 3, 1);
const pikKoenig = card(2, 3, 4);
const kreuzAss = card(3, 4, 6);
const kreuzNeun = card(4, 4, 1);
const herzDame = card(5, 2, 3, true);
const herzZehn = card(6, 2, 5, true);

function game(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return {
    id: 1,
    phase: 3,
    dran: 12,
    stichNr: 1,
    vorhand: 11,
    aktuellerStich: { karten: [], stecher: null },
    letzterStich: { karten: [], stecher: null },
    spieler: {},
    messages: [],
    spieltyp: '',
    schweinchen: false,
    kontrasOffengelegt: 0,
    reAnsage: false,
    kontraAnsage: false,
    reAbsage: null,
    kontraAbsage: null,
    ...overrides,
  };
}

function ich(id: number, hand: LiveEngineCard[]): LiveSpieler {
  return {
    id,
    index: 0,
    hand,
    armutKarten: [],
    moeglicheVorbehalte: [],
    vorbehalt: 'Gesund',
    isRe: null,
    ansage: null,
    absage: null,
    moeglicheAnAbsage: null,
    punkte: null,
    parteiOffengelegt: 0,
  };
}

function renderShow(viewerPlayerId: number, liveGame: LiveGameData, hand: LiveEngineCard[]) {
  const props: LiveShowProps = {
    round: {
      id: 7,
      path: '/runde/7',
      gamesCount: 0,
      liveRoundId: 5,
      rules,
      viewerPlayerId,
      players,
    },
    liveGame,
    lastLiveGame: null,
    ich: ich(viewerPlayerId, hand),
  };
  const view = renderWithUi(Show, props);
  echo.onlineMembers!.value = members;

  return view;
}

function cardButton(name: string): HTMLButtonElement {
  return screen.getByRole('button', { name }) as HTMLButtonElement;
}

beforeEach(() => {
  echo.options = null;
  echo.onlineMembers!.value = [];
  postLive.mockReset();
  postLive.mockResolvedValue(undefined);
  vi.spyOn(router, 'reload').mockImplementation(() => undefined);
});

describe('Live-Seite: Armut abgeben', () => {
  const hand = [pikNeun, pikKoenig, kreuzAss, kreuzNeun];

  it('hebt gewählte Karten an, legt sie per Antippen zurück und gibt erst bei drei Karten ab', async () => {
    renderShow(12, game(), hand);

    await screen.findByText('Wähle aus, welche Karten du abgeben möchtest!');
    expect(screen.queryByRole('button', { name: 'Karten abgeben' })).toBeNull();

    await userEvent.click(cardButton('Pik 9'));
    await userEvent.click(cardButton('Pik König'));
    expect(cardButton('Pik 9').getAttribute('aria-pressed')).toBe('true');
    expect(cardButton('Kreuz Ass').getAttribute('aria-pressed')).toBe('false');
    expect(screen.queryByRole('button', { name: 'Karten abgeben' })).toBeNull();

    await userEvent.click(cardButton('Kreuz Ass'));
    await userEvent.click(screen.getByRole('button', { name: 'Karten abgeben' }));

    expect(postLive).toHaveBeenCalledWith('/api/live/1/armutAbgeben', {
      karten: [{ id: 1 }, { id: 2 }, { id: 3 }],
    });
  });

  it('lässt die vierte Karte nicht zu und nimmt eine zurückgelegte wieder heraus', async () => {
    renderShow(12, game(), hand);
    await screen.findByText('Wähle aus, welche Karten du abgeben möchtest!');

    for (const name of ['Pik 9', 'Pik König', 'Kreuz Ass', 'Kreuz 9']) {
      await userEvent.click(cardButton(name));
    }
    expect(cardButton('Kreuz 9').getAttribute('aria-pressed')).toBe('false');

    await userEvent.click(cardButton('Pik 9'));
    expect(cardButton('Pik 9').getAttribute('aria-pressed')).toBe('false');
    expect(screen.queryByRole('button', { name: 'Karten abgeben' })).toBeNull();
  });

  it('verlangt den ganzen Trumpf und sendet nichts, solange er auf der Hand bleibt', async () => {
    renderShow(12, game(), [pikNeun, pikKoenig, kreuzAss, herzDame, herzZehn]);
    await screen.findByText('Wähle aus, welche Karten du abgeben möchtest!');

    for (const name of ['Pik 9', 'Pik König', 'Kreuz Ass']) {
      await userEvent.click(cardButton(name));
    }
    await userEvent.click(screen.getByRole('button', { name: 'Karten abgeben' }));

    expect(screen.getByText('Du musst all deinen Trumpf abgeben!')).toBeTruthy();
    expect(postLive).not.toHaveBeenCalled();
  });

  it('lässt die anderen warten, ihre Hand bleibt nur zum Anschauen', async () => {
    renderShow(13, game(), hand);

    await screen.findByText('Die Armut wählt ihre Karten.');
    expect(screen.getByRole('img', { name: 'Pik 9' })).toBeTruthy();
    expect(screen.queryByRole('button', { name: 'Pik 9' })).toBeNull();
  });
});

describe('Live-Seite: Armut mitnehmen und zurückgeben', () => {
  const hand = [pikNeun, pikKoenig, kreuzAss, kreuzNeun];

  it('fragt den Spieler, der dran ist, und sendet Ja', async () => {
    renderShow(13, game({ phase: 32, dran: 13 }), hand);

    await userEvent.click(await screen.findByRole('button', { name: 'Ja!' }));

    expect(postLive).toHaveBeenCalledWith('/api/live/1/armutMitnehmen', { mitnehmen: true });
  });

  it('sendet Nein', async () => {
    renderShow(13, game({ phase: 32, dran: 13 }), hand);

    await userEvent.click(await screen.findByRole('button', { name: 'Nein!' }));

    expect(postLive).toHaveBeenCalledWith('/api/live/1/armutMitnehmen', { mitnehmen: false });
  });

  it('zeigt den anderen, wer überlegt', async () => {
    renderShow(12, game({ phase: 32, dran: 13 }), hand);

    expect(await screen.findByText('Dieter überlegt …')).toBeTruthy();
  });

  it('lässt den Mitnehmer drei Karten zurückgeben', async () => {
    renderShow(13, game({ phase: 33, dran: 13 }), hand);
    await screen.findByText('Wähle aus, welche Karten du zurückgeben möchtest!');

    for (const name of ['Pik 9', 'Kreuz Ass', 'Kreuz 9']) {
      await userEvent.click(cardButton(name));
    }
    await userEvent.click(screen.getByRole('button', { name: 'Karten zurückgeben' }));

    expect(postLive).toHaveBeenCalledWith('/api/live/1/armutZurueck', {
      karten: [{ id: 1 }, { id: 3 }, { id: 4 }],
    });
  });

  it('wechselt per Push von der Auswahl zur Frage, ohne alte Auswahl mitzunehmen', async () => {
    renderShow(12, game(), hand);
    await userEvent.click(await screen.findByRole('button', { name: 'Pik 9' }));

    echo.options!.onPush({ liveGame: game({ phase: 32, dran: 13 }), ich: ich(12, hand) });

    expect(await screen.findByText('Dieter überlegt …')).toBeTruthy();
    await waitFor(() => expect(screen.getByRole('img', { name: 'Pik 9' })).toBeTruthy());
    expect(screen.queryByRole('button', { name: 'Pik 9' })).toBeNull();
  });
});
