import { type Ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { screen, within, waitFor } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import Show from '@/Pages/Live/Show.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveEchoMember, UseLiveEchoOptions } from '@/composables/useLiveEcho';
import type {
  LiveEngineCard,
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

const reloadPage = vi.hoisted(() => vi.fn());
vi.mock('@/composables/reloadPage', () => ({ reloadPage }));

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
  reloadPage.mockReset();
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

function engineCard(id: number, farbe: 1 | 2 | 3 | 4, wert: 1 | 2 | 3 | 4 | 5 | 6): LiveEngineCard {
  return {
    id,
    farbe,
    wert,
    rang: 0,
    punkte: 0,
    trumpf: false,
    spielbar: true,
    gespieltVon: false,
    armut_zurueck: false,
  };
}

const SOLOS = [
  'Fleischlos',
  'Bubensolo',
  'Damensolo',
  'Königssolo',
  'Trumpfsolo',
  'Farbsolo Herz',
  'Farbsolo Pik',
  'Farbsolo Kreuz',
];

/** Clara (12) mit zwei Karten und allen Soli; sie hat noch keinen Vorbehalt gemeldet. */
function claraWithHand(overrides: Partial<LiveSpieler> = {}): LiveSpieler {
  return {
    ...ich(),
    hand: [engineCard(1, 4, 3), engineCard(2, 2, 5)],
    moeglicheVorbehalte: SOLOS,
    ...overrides,
  };
}

describe('Live-Seite: Hand und Vorbehalte', () => {
  it('zeigt meine Hand, sobald das Spiel läuft und alle online sind', async () => {
    renderShow(12, { liveGame: game({ phase: 2, dran: 11, vorhand: 11 }), ich: claraWithHand() });
    setOnline(...activeMembers);

    const handGroup = await screen.findByRole('group', { name: 'Deine Hand' });
    expect(
      within(handGroup)
        .getAllByRole('img')
        .map((card) => card.getAttribute('aria-label')),
    ).toEqual(['Kreuz Dame', 'Herz 10']);
  });

  it('zeigt die Hand nicht, solange nicht alle online sind', async () => {
    renderShow(12, { liveGame: game({ phase: 2 }), ich: claraWithHand() });
    setOnline(member(11, 'Bernd'), member(12, 'Clara'));

    await screen.findByText('Bitte warte, bis alle online sind');
    expect(screen.queryByRole('group', { name: 'Deine Hand' })).toBeNull();
    expect(screen.queryByText('Mögliche Vorbehalte:')).toBeNull();
  });

  it('zeigt Zuschauern und wer aussetzt weder Hand noch Vorbehalte', async () => {
    renderShow(10, { liveGame: game({ phase: 2 }), ich: null });
    setOnline(...activeMembers, member(10, 'Anna'));

    await screen.findByText('Du setzt dieses Spiel aus!');
    expect(screen.queryByRole('group', { name: 'Deine Hand' })).toBeNull();
    expect(screen.queryByText('Mögliche Vorbehalte:')).toBeNull();
  });

  it('zeigt die möglichen Vorbehalte gesperrt, solange ein anderer dran ist', async () => {
    renderShow(12, { liveGame: game({ phase: 2, dran: 11, vorhand: 11 }), ich: claraWithHand() });
    setOnline(...activeMembers);

    expect(await screen.findByText('Mögliche Vorbehalte:')).toBeTruthy();
    expect((screen.getByRole('button', { name: 'Gesund' }) as HTMLButtonElement).disabled).toBe(
      true,
    );
  });

  it('meldet Vorhand und gemeldete Vorbehalte an den Badges', async () => {
    const spieler = { 11: display(), 12: display(), 13: display(), 14: display() };
    spieler[11].vorbehalt = 'Gesund';
    spieler[13].vorbehalt = 'Vorbehalt';
    renderShow(12, {
      liveGame: game({ phase: 2, dran: 12, vorhand: 11, spieler }),
      ich: claraWithHand(),
    });
    setOnline(...activeMembers);

    const seatOf = (name: string) => screen.getByRole('group', { name: new RegExp(`^${name},`) });
    await waitFor(() => expect(seatOf('Bernd').textContent).toContain('Vorhand · Gesund'));
    expect(seatOf('Dieter').textContent).toContain('Vorbehalt');
    expect(seatOf('Dieter').textContent).not.toContain('Vorhand');
  });

  it('sendet meinen Vorbehalt an die Engine und sperrt die Buttons, bis sie antwortet', async () => {
    let answer!: () => void;
    postLive.mockReturnValue(new Promise<void>((resolve) => (answer = resolve)));
    renderShow(12, { liveGame: game({ phase: 2, dran: 12, vorhand: 12 }), ich: claraWithHand() });
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Gesund' }));

    expect(postLive).toHaveBeenCalledWith('/api/live/1/vorbehalt', { vorbehalt: 'Gesund' });
    expect((screen.getByRole('button', { name: 'Bubensolo' }) as HTMLButtonElement).disabled).toBe(
      true,
    );
    answer();
    await waitFor(() =>
      expect(
        (screen.getByRole('button', { name: 'Bubensolo' }) as HTMLButtonElement).disabled,
      ).toBe(false),
    );
  });

  it('bittet nach meiner Wahl zu warten, sobald der Push meinen Vorbehalt bringt', async () => {
    renderShow(12, { liveGame: game({ phase: 2, dran: 12, vorhand: 12 }), ich: claraWithHand() });
    setOnline(...activeMembers);
    await screen.findByText('Wähle deinen Vorbehalt:');

    echo.options!.onPush({
      liveGame: game({ phase: 2, dran: 13, vorhand: 12 }),
      ich: claraWithHand({ vorbehalt: 'Gesund' }),
    });

    expect(
      await screen.findByText('Bitte warte, bis alle ihren Vorbehalt bestimmt haben.'),
    ).toBeTruthy();
    expect(screen.queryByRole('button', { name: 'Gesund' })).toBeNull();
    expect(screen.getByRole('group', { name: 'Deine Hand' })).toBeTruthy();
  });

  it('blendet die Vorbehalte mit Beginn des Spiels aus und lässt die Hand stehen', async () => {
    renderShow(12, { liveGame: game({ phase: 2, dran: 12 }), ich: claraWithHand() });
    setOnline(...activeMembers);
    await screen.findByText('Wähle deinen Vorbehalt:');

    echo.options!.onPush({ liveGame: game({ phase: 4, dran: 11 }), ich: claraWithHand() });

    await waitFor(() => expect(screen.queryByText('Wähle deinen Vorbehalt:')).toBeNull());
    expect(screen.getByRole('group', { name: 'Deine Hand' })).toBeTruthy();
  });

  it('meldet einen abgelehnten Vorbehalt (422) mit Toast und lädt die Daten neu', async () => {
    postLive.mockRejectedValue({
      response: { status: 422, data: '{"message":"Dieser Vorbehalt ist nicht möglich!"}' },
    });
    renderShow(12, { liveGame: game({ phase: 2, dran: 12, vorhand: 12 }), ich: claraWithHand() });
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Gesund' }));

    expect(await screen.findByText('Dieser Vorbehalt ist nicht möglich!')).toBeTruthy();
    expect(router.reload).toHaveBeenCalledWith(
      expect.objectContaining({ only: ['round', 'liveGame', 'lastLiveGame', 'ich'] }),
    );
  });

  it('lädt die ganze Seite neu, wenn die Sitzung abgelaufen ist (419)', async () => {
    postLive.mockRejectedValue({ response: { status: 419, data: '' } });
    renderShow(12, { liveGame: game({ phase: 2, dran: 12, vorhand: 12 }), ich: claraWithHand() });
    setOnline(...activeMembers);

    await userEvent.click(await screen.findByRole('button', { name: 'Gesund' }));

    await waitFor(() => expect(reloadPage).toHaveBeenCalledTimes(1));
  });
});
