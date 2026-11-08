import { ref, type Ref } from 'vue';
import { vi } from 'vitest';
import type { LiveEchoMember, UseLiveEchoOptions } from '@/composables/useLiveEcho';

/**
 * Ersetzt die Echo-Anbindung der Live-Seite: Der Test löst Pushes über `echo.options` aus und
 * stellt die Online-Liste ein. Einbinden in der Testdatei:
 *
 * ```ts
 * vi.mock('@/composables/useLiveEcho', async () => (await import('@/testing/liveEcho')).liveEchoModule());
 * ```
 */
export const echo = {
  options: null as UseLiveEchoOptions | null,
  onlineMembers: ref<LiveEchoMember[]>([]) as Ref<LiveEchoMember[]>,
  sendReady: vi.fn(),
};

export function liveEchoModule() {
  return {
    useLiveEcho: (options: UseLiveEchoOptions) => {
      echo.options = options;

      return { onlineMembers: echo.onlineMembers, sendReady: echo.sendReady };
    },
  };
}

/** Setzt die Echo-Attrappe zurück; gehört in das `beforeEach` der Testdatei. */
export function resetLiveEcho(): void {
  echo.options = null;
  echo.onlineMembers.value = [];
  echo.sendReady.mockReset();
}
