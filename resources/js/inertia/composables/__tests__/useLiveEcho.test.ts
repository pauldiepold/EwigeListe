import { defineComponent, h, nextTick, ref } from 'vue';
import { render } from '@testing-library/vue';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import {
  useLiveEcho,
  type UseLiveEchoOptions,
  type UseLiveEchoReturn,
} from '@/composables/useLiveEcho';

type Handler = (payload: never) => void;

/**
 * Ein Echo ohne Netz: hält fest, wo gelauscht wird, und lässt den Test Ereignisse auslösen. Der
 * Presence-Channel bildet den Pusher-Channel nach (`subscription` mit `subscribed`, `members`,
 * `bind`), denn Echo-Callbacks wie `here()` feuern nur beim Abonnieren und nie nachträglich.
 */
const fake = vi.hoisted(() => {
  const state = {
    presenceName: '',
    roundUpdated: null as (() => void) | null,
    whisperHandlers: {} as Record<string, Handler>,
    bindings: {} as Record<string, Handler[]>,
    subscribed: false,
    members: [] as { id: number; name: string; avatar_path: string | null }[],
    whisper: vi.fn(),
    privateListeners: {} as Record<string, Record<string, Handler>>,
    leaveChannel: vi.fn(),
    disconnect: vi.fn(),
    configureEcho: vi.fn(),
  };

  const subscription = {
    get subscribed() {
      return state.subscribed;
    },
    members: {
      each: (callback: (member: { id: string; info: unknown }) => void) =>
        state.members.forEach((info) => callback({ id: String(info.id), info })),
    },
    bind: (event: string, handler: Handler) => {
      state.bindings[event] = [...(state.bindings[event] ?? []), handler];
    },
    unbind: (event: string, handler: Handler) => {
      state.bindings[event] = (state.bindings[event] ?? []).filter((bound) => bound !== handler);
    },
  };

  const presence = {
    subscription,
    listenForWhisper: (event: string, handler: Handler) => (
      (state.whisperHandlers[event] = handler),
      presence
    ),
    stopListeningForWhisper: (event: string, handler: Handler) => {
      if (state.whisperHandlers[event] === handler) {
        delete state.whisperHandlers[event];
      }

      return presence;
    },
    whisper: state.whisper,
  };

  /** Pusher ruft die gebundenen Handler eines Ereignisses nacheinander auf. */
  function emit(event: string, payload: unknown): void {
    (state.bindings[event] ?? []).forEach((handler) => handler(payload as never));
  }

  const connection = {
    private: (name: string) => ({
      listen: (event: string, handler: Handler) => {
        state.privateListeners[name] = { ...state.privateListeners[name], [event]: handler };
      },
    }),
    leaveChannel: state.leaveChannel,
    disconnect: state.disconnect,
    socketId: () => 'socket-1',
  };

  return { state, presence, connection, emit };
});

vi.mock('@laravel/echo-vue', () => ({
  configureEcho: fake.state.configureEcho,
  echo: () => fake.connection,
  useEchoPresence: (name: string, _event: string, callback: () => void) => {
    fake.state.presenceName = name;
    fake.state.roundUpdated = callback;

    return { channel: () => fake.presence };
  },
}));

function mountLiveEcho(overrides: Partial<UseLiveEchoOptions> = {}) {
  const isActivePlayer = ref(true);
  const options: UseLiveEchoOptions = {
    roundId: 7,
    liveRoundId: 5,
    playerId: 12,
    isActivePlayer,
    onPush: vi.fn(),
    onRoundUpdated: vi.fn(),
    onReady: vi.fn(),
    onMemberLeft: vi.fn(),
    ...overrides,
  };
  let result!: UseLiveEchoReturn;

  const Harness = defineComponent({
    setup() {
      result = useLiveEcho(options);

      return () => h('div');
    },
  });
  const view = render(Harness);

  return { ...view, options, isActivePlayer, result };
}

beforeEach(() => {
  vi.stubEnv('VITE_PUSHER_APP_KEY', 'test-key');
  fake.state.privateListeners = {};
  fake.state.whisperHandlers = {};
  fake.state.bindings = {};
  fake.state.members = [];
  fake.state.subscribed = false;
});

afterEach(() => {
  vi.unstubAllEnvs();
  fake.state.whisper.mockReset();
  fake.state.leaveChannel.mockReset();
  fake.state.disconnect.mockReset();
  fake.state.configureEcho.mockReset();
});

describe('useLiveEcho', () => {
  it('führt die Online-Liste über den Presence-Channel der Runde', () => {
    const { result } = mountLiveEcho();
    const anna = { id: 11, name: 'Anna', avatar_path: null };
    const bernd = { id: 12, name: 'Bernd', avatar_path: null };

    expect(fake.state.presenceName).toBe('round.7');

    fake.state.members = [anna];
    fake.state.subscribed = true;
    fake.emit('pusher:subscription_succeeded', {});
    fake.emit('pusher:member_added', { id: '12', info: bernd });
    fake.emit('pusher:member_added', { id: '12', info: bernd });
    expect(result.onlineMembers.value).toEqual([anna, bernd]);

    fake.emit('pusher:member_removed', { id: '11', info: anna });
    expect(result.onlineMembers.value).toEqual([bernd]);
  });

  it('übernimmt die Mitglieder sofort, wenn der Channel schon abonniert ist', () => {
    const anna = { id: 11, name: 'Anna', avatar_path: null };
    const bernd = { id: 12, name: 'Bernd', avatar_path: null };
    // Die Runden-Seite hat den Channel schon geöffnet; „subscription_succeeded“ ist vorbei.
    fake.state.members = [anna, bernd];
    fake.state.subscribed = true;

    const { result } = mountLiveEcho();

    expect(result.onlineMembers.value).toEqual([anna, bernd]);
  });

  it('entfernt seine Listener beim Verlassen der Seite, damit sie sich auf dem geteilten Channel nicht stapeln', () => {
    const { unmount } = mountLiveEcho();

    unmount();

    expect(fake.state.bindings['pusher:subscription_succeeded']).toEqual([]);
    expect(fake.state.bindings['pusher:member_added']).toEqual([]);
    expect(fake.state.bindings['pusher:member_removed']).toEqual([]);
    expect(fake.state.whisperHandlers.ready).toBeUndefined();
  });

  it('meldet, wenn jemand den Channel verlässt, und wenn jemand „bereit“ flüstert', () => {
    const { options } = mountLiveEcho();

    fake.emit('pusher:member_removed', {
      id: '11',
      info: { id: 11, name: 'Anna', avatar_path: null },
    });
    fake.state.whisperHandlers.ready({ id: 13 } as never);

    expect(options.onMemberLeft).toHaveBeenCalledTimes(1);
    expect(options.onReady).toHaveBeenCalledWith(13);
  });

  it('flüstert „bereit“ mit meiner Spieler-ID', () => {
    const { result } = mountLiveEcho();

    result.sendReady(12);

    expect(fake.state.whisper).toHaveBeenCalledWith('ready', { id: 12 });
  });

  it('meldet RoundUpdated', () => {
    const { options } = mountLiveEcho();

    fake.state.roundUpdated!();

    expect(options.onRoundUpdated).toHaveBeenCalledTimes(1);
  });

  it('hört als Mitspieler auf den eigenen Channel und übergibt den Push mit Hand', () => {
    const { options } = mountLiveEcho();
    const push = { liveGame: { id: 1 }, ich: { id: 12 } };

    expect(Object.keys(fake.state.privateListeners)).toEqual(['liveRound.5.12']);
    fake.state.privateListeners['liveRound.5.12'].LiveGameDataBroadcasted(push as never);

    expect(options.onPush).toHaveBeenCalledWith(push);
  });

  it('hört als Zuschauer auf den gemeinsamen Channel', () => {
    const { options } = mountLiveEcho({ isActivePlayer: ref(false) });
    const push = { liveGame: { id: 1 } };

    expect(Object.keys(fake.state.privateListeners)).toEqual(['liveRound.5']);
    fake.state.privateListeners['liveRound.5'].LiveGameDataBroadcastedInaktiv(push as never);

    expect(options.onPush).toHaveBeenCalledWith(push);
  });

  it('wechselt den Channel, wenn ich im nächsten Spiel mitspiele', async () => {
    const isActivePlayer = ref(false);
    mountLiveEcho({ isActivePlayer });

    isActivePlayer.value = true;
    await nextTick();

    expect(fake.state.leaveChannel).toHaveBeenCalledWith('private-liveRound.5');
    expect(Object.keys(fake.state.privateListeners)).toContain('liveRound.5.12');
  });

  it('verlässt den privaten Channel und trennt die Verbindung beim Verlassen der Seite', () => {
    const { unmount } = mountLiveEcho();

    unmount();

    expect(fake.state.leaveChannel).toHaveBeenCalledWith('private-liveRound.5.12');
    expect(fake.state.disconnect).toHaveBeenCalledTimes(1);
  });

  it('verbindet sich nicht ohne Pusher-Key', () => {
    vi.stubEnv('VITE_PUSHER_APP_KEY', '');

    const { result } = mountLiveEcho();

    expect(fake.state.configureEcho).not.toHaveBeenCalled();
    expect(result.onlineMembers.value).toEqual([]);
    expect(() => result.sendReady(12)).not.toThrow();
  });
});
