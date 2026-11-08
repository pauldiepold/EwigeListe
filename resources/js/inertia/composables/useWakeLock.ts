import { onMounted, onUnmounted } from 'vue';

/**
 * Hält den Bildschirm an, solange die Seite offen und sichtbar ist (Screen Wake Lock, wo der
 * Browser ihn kennt). Der Browser gibt die Sperre frei, sobald die Seite verdeckt wird; beim
 * Zurückkehren wird sie neu angefordert.
 */
export function useWakeLock(): void {
  let sentinel: WakeLockSentinel | null = null;
  let stopped = false;

  async function acquire(): Promise<void> {
    if (
      !('wakeLock' in navigator) ||
      document.visibilityState !== 'visible' ||
      (sentinel && !sentinel.released)
    ) {
      return;
    }

    try {
      const next = await navigator.wakeLock.request('screen');

      if (stopped) {
        await next.release();

        return;
      }

      sentinel = next;
    } catch {
      // Verweigert (z. B. Stromsparmodus): Das Handy darf dann eben sperren.
    }
  }

  function acquireWhenVisible(): void {
    void acquire();
  }

  onMounted(() => {
    document.addEventListener('visibilitychange', acquireWhenVisible);
    void acquire();
  });

  onUnmounted(() => {
    stopped = true;
    document.removeEventListener('visibilitychange', acquireWhenVisible);
    void sentinel?.release();
    sentinel = null;
  });
}
