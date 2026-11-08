import { type Ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { screen, within, waitFor } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import Show from '@/Pages/Live/Show.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveEchoMember, UseLiveEchoOptions } from '@/composables/useLiveEcho';
import type {
  LiveGameData,
  LiveGamePush,
  LivePlayer,
  LiveShowProps,
  LiveSpieler,
} from '@/types/live';

/** Ersetzt die Echo-Anbindung: Der Test löst Pushes aus und stellt die Online-Liste ein. */
const echo = vi.hoisted(() => ({
  options: null as UseLiveEchoOptions | null,
  onlineMembers: null as Ref<LiveEchoMember[]> | null,
  sendReady: vi.fn(),
}));

vi.mock('@/composables/useLiveEcho', async () => {
  const { ref } = await import('vue');
  echo.onlineMembers = ref([]);

  return {
    useLiveEcho: (options: UseLiveEchoOptions) => {
      echo.options = options;

      return { onlineMembers: echo.onlineMembers, sendReady: echo.sendReady };
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

function player(seat: number, name: string, isActive = true): LivePlayer {
  return {
    id: 10 + seat,
    firstName: name,
    lastName: 'Test',
    fullName: `${name} Test`,
    avatarPath: null,
    isActive,
  };
}

/** Anna setzt aus; Bernd (11) ist der erste aktive Spieler am Tisch. */
const players = [
  player(0, 'Anna', false),
  player(1, 'Bernd'),
  player(2, 'Clara'),
  player(3, 'Dieter'),
  player(4, 'Eva'),
];

function member(id: number, name: string): LiveEchoMember {
  return { id, name: `${name} Test`, avatar_path: null };
}

const activeMembers = [
  member(11, 'Bernd'),
  member(12, 'Clara'),
  member(13, 'Dieter'),
  member(14, 'Eva'),
];

function display() {
  return { ansage: '', spieltyp: '', partei: '', vorbehalt: '' };
}

function game(overrides: Partial<LiveGameData> = {}): LiveGameData {
  return {
    id: 1,
    phase: 4,
    dran: 11,
    stichNr: 1,
    vorhand: 11,
    aktuellerStich: { karten: [], stecher: null },
    letzterStich: { karten: [], stecher: null },
    spieler: { 11: display(), 12: display(), 13: display(), 14: display() },
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

function ich(): LiveSpieler {
  return {
    id: 12,
    index: 0,
    hand: [],
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

function pageProps(viewerPlayerId: number, overrides: Partial<LiveShowProps> = {}): LiveShowProps {
  return {
    round: {
      id: 7,
      path: '/runde/7',
      gamesCount: 0,
      liveRoundId: 5,
      rules,
      viewerPlayerId,
      players,
    },
    liveGame: null,
    lastLiveGame: null,
    ich: null,
    ...overrides,
  };
}

function renderShow(viewerPlayerId: number, overrides: Partial<LiveShowProps> = {}) {
  return renderWithUi(Show, pageProps(viewerPlayerId, overrides));
}

function setOnline(...members: LiveEchoMember[]): void {
  echo.onlineMembers!.value = members;
}

function setVisibility(state: 'visible' | 'hidden'): void {
  Object.defineProperty(document, 'visibilityState', { value: state, configurable: true });
  document.dispatchEvent(new Event('visibilitychange'));
}

beforeEach(() => {
  echo.options = null;
  echo.onlineMembers!.value = [];
  echo.sendReady.mockReset();
  postLive.mockReset();
  postLive.mockResolvedValue(undefined);
  vi.spyOn(router, 'reload').mockImplementation(() => undefined);
});

afterEach(() => {
  setVisibility('visible');
});

describe('Live-Seite: Warten auf Spieler', () => {
  it('bittet zu warten, solange nicht alle Spieler des Spiels online sind, und markiert die Online-Spieler', async () => {
    renderShow(12);
    setOnline(member(11, 'Bernd'), member(12, 'Clara'));

    await waitFor(() => {
      expect(screen.getByText('Bitte warte, bis alle online sind')).toBeTruthy();
    });
    const bernd = screen.getByRole('listitem', { name: 'Bernd, online' });
    expect(within(bernd).getByText('Bernd')).toBeTruthy();
    expect(screen.getByRole('listitem', { name: 'Dieter, offline' })).toBeTruthy();
  });

  it('listet nur die Spieler des Spiels, nicht den, der aussetzt', async () => {
    renderShow(12);

    await screen.findByText('Bitte warte, bis alle online sind');
    expect(screen.queryByRole('listitem', { name: /Anna/ })).toBeNull();
  });

  it('zeigt Zuschauer am Rand, auch den, der aussetzt', async () => {
    renderShow(12);
    setOnline(...activeMembers, member(10, 'Anna'), member(99, 'Gast'));

    const spectators = await screen.findByRole('list', { name: 'Zuschauer' });
    expect(within(spectators).getByRole('listitem', { name: 'Anna Test' })).toBeTruthy();
    expect(within(spectators).getByRole('listitem', { name: 'Gast Test' })).toBeTruthy();
    expect(within(spectators).queryByRole('listitem', { name: 'Bernd Test' })).toBeNull();
  });
});

describe('Live-Seite: Bereit-Check', () => {
  it('fragt nach dem Bereit-Check, sobald alle online sind und kein Spiel läuft', async () => {
    renderShow(12);
    setOnline(...activeMembers);

    expect(await screen.findByRole('button', { name: 'Bereit?' })).toBeTruthy();
    expect(screen.queryByText('Bitte warte, bis alle online sind')).toBeNull();
    expect(screen.getByText('Das erste Spiel startet, sobald alle bereit sind')).toBeTruthy();
  });

  it('spricht nach dem ersten Spiel vom nächsten Spiel', async () => {
    const props = pageProps(12);
    renderWithUi(Show, { ...props, round: { ...props.round, gamesCount: 1 } });
    setOnline(...activeMembers);

    expect(
      await screen.findByText('Das nächste Spiel startet, sobald alle bereit sind'),
    ).toBeTruthy();
  });

  it('flüstert „bereit“ und merkt mich als bereit', async () => {
    renderShow(12);
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Bereit?' }));

    expect(echo.sendReady).toHaveBeenCalledWith(12);
    expect(screen.queryByRole('button', { name: 'Bereit?' })).toBeNull();
    expect(screen.getByRole('listitem', { name: 'Clara, bereit' })).toBeTruthy();
    expect(screen.getByRole('listitem', { name: 'Dieter, nicht bereit' })).toBeTruthy();
  });

  it('startet das Spiel im Browser des ersten Spielers, sobald alle bereit sind', async () => {
    renderShow(11);
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Bereit?' }));
    expect(postLive).not.toHaveBeenCalled();

    echo.options!.onReady(12);
    echo.options!.onReady(13);
    echo.options!.onReady(14);

    await waitFor(() => expect(postLive).toHaveBeenCalledTimes(1));
    expect(postLive).toHaveBeenCalledWith('/api/live/5/spielStarten');
  });

  it('startet das Spiel nicht in den Browsern der anderen Spieler', async () => {
    renderShow(12);
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Bereit?' }));
    echo.options!.onReady(11);
    echo.options!.onReady(13);
    echo.options!.onReady(14);

    await screen.findByRole('listitem', { name: 'Eva, bereit' });
    expect(postLive).not.toHaveBeenCalled();
  });

  it('setzt die Bereiten zurück, sobald jemand den Channel verlässt', async () => {
    renderShow(12);
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Bereit?' }));
    echo.options!.onMemberLeft();

    expect(await screen.findByRole('button', { name: 'Bereit?' })).toBeTruthy();
    expect(screen.getByRole('listitem', { name: 'Clara, nicht bereit' })).toBeTruthy();
  });

  it('lässt wer aussetzt zusehen, ohne Bereit-Button', async () => {
    renderShow(10);
    setOnline(...activeMembers, member(10, 'Anna'));

    await screen.findByText('Das erste Spiel startet, sobald alle bereit sind');
    expect(screen.queryByRole('button', { name: 'Bereit?' })).toBeNull();
  });

  it('zeigt den Bereit-Check nicht, solange ein Spiel läuft', async () => {
    renderShow(12, { liveGame: game(), ich: ich() });
    setOnline(...activeMembers);

    await waitFor(() => expect(screen.getAllByRole('group').length).toBe(4));
    expect(screen.queryByRole('button', { name: 'Bereit?' })).toBeNull();
  });
});

describe('Live-Seite: Pushes und Neuladen', () => {
  it('übernimmt einen Push ohne Neuladen in den Tisch', async () => {
    renderShow(12, { liveGame: game({ dran: 11 }), ich: ich() });
    setOnline(...activeMembers);

    const seatOf = (name: string) => screen.getByRole('group', { name: new RegExp(`^${name},`) });
    await waitFor(() => expect(within(seatOf('Bernd')).getByText('dran')).toBeTruthy());

    const push: LiveGamePush = { liveGame: game({ dran: 13 }), ich: ich() };
    echo.options!.onPush(push);

    await waitFor(() => expect(within(seatOf('Dieter')).getByText('dran')).toBeTruthy());
    expect(within(seatOf('Bernd')).queryByText('dran')).toBeNull();
    expect(router.reload).not.toHaveBeenCalled();
  });

  it('meldet Echo meine Rolle: Spieler des Spiels hören auf ihren Channel, Zuschauer nicht', () => {
    const { unmount } = renderShow(12);
    expect(echo.options).toMatchObject({ roundId: 7, liveRoundId: 5, playerId: 12 });
    expect(echo.options!.isActivePlayer.value).toBe(true);
    unmount();

    renderShow(10);
    expect(echo.options!.isActivePlayer.value).toBe(false);
  });

  it('lädt die Props bei RoundUpdated neu', () => {
    renderShow(12);

    echo.options!.onRoundUpdated();

    expect(router.reload).toHaveBeenCalledTimes(1);
    expect(router.reload).toHaveBeenCalledWith(
      expect.objectContaining({ only: ['round', 'liveGame', 'lastLiveGame', 'ich'] }),
    );
  });

  it('lädt neu, sobald die Seite wieder sichtbar wird', () => {
    renderShow(12);
    setVisibility('hidden');
    expect(router.reload).not.toHaveBeenCalled();

    setVisibility('visible');

    expect(router.reload).toHaveBeenCalledTimes(1);
  });

  it('lädt per Button „Daten neu laden“ neu', async () => {
    renderShow(12);

    await userEvent.click(screen.getByRole('button', { name: 'Daten neu laden' }));

    expect(router.reload).toHaveBeenCalledTimes(1);
  });
});
