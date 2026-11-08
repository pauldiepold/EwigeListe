import { defineComponent, h } from 'vue';
import { render } from '@testing-library/vue';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { useRoundUpdates } from '@/composables/useRoundUpdates';

type Handler = (payload: unknown) => void;

/** Ein Echo ohne Netz mit einem Pusher-Presence-Channel, der schon abonniert sein kann. */
const fake = vi.hoisted(() => {
  const state = {
    bindings: {} as Record<string, Handler[]>,
    subscribed: false,
    members: [] as { id: number }[],
    roundUpdated: null as (() => void) | null,
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

  function emit(event: string, payload: unknown): void {
    (state.bindings[event] ?? []).forEach((handler) => handler(payload));
  }

  return { state, subscription, emit };
});

vi.mock('@laravel/echo-vue', () => ({
  configureEcho: vi.fn(),
  echo: () => ({ socketId: () => null, disconnect: vi.fn() }),
  useEchoPresence: (_name: string, _event: string, callback: () => void) => {
    fake.state.roundUpdated = callback;

    return { channel: () => ({ subscription: fake.subscription }) };
  },
}));

function mountRoundUpdates(refresh = vi.fn()) {
  let result!: ReturnType<typeof useRoundUpdates>;
  const Harness = defineComponent({
    setup() {
      result = useRoundUpdates(7, refresh);

      return () => h('div');
    },
  });
  const view = render(Harness);

  return { ...view, refresh, result };
}

beforeEach(() => {
  vi.stubEnv('VITE_PUSHER_APP_KEY', 'test-key');
  fake.state.bindings = {};
  fake.state.members = [];
  fake.state.subscribed = false;
});

afterEach(() => {
  vi.unstubAllEnvs();
});

describe('useRoundUpdates', () => {
  it('führt die Online-Spieler über den Presence-Channel der Runde', () => {
    const { result } = mountRoundUpdates();

    fake.state.members = [{ id: 11 }];
    fake.state.subscribed = true;
    fake.emit('pusher:subscription_succeeded', {});
    fake.emit('pusher:member_added', { id: '12', info: { id: 12 } });
    expect(result.onlinePlayerIds.value).toEqual([11, 12]);

    fake.emit('pusher:member_removed', { id: '11', info: { id: 11 } });
    expect(result.onlinePlayerIds.value).toEqual([12]);
  });

  it('übernimmt die Spieler sofort, wenn die Live-Seite den Channel schon abonniert hat', () => {
    fake.state.members = [{ id: 11 }, { id: 12 }];
    fake.state.subscribed = true;

    const { result } = mountRoundUpdates();

    expect(result.onlinePlayerIds.value).toEqual([11, 12]);
  });

  it('lädt die Runde bei RoundUpdated neu', () => {
    const { refresh } = mountRoundUpdates();

    fake.state.roundUpdated!();

    expect(refresh).toHaveBeenCalledTimes(1);
  });
});
