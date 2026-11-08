import { onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { configureEcho, echo } from '@laravel/echo-vue';

function xsrfToken(): string {
  const cookie = document.cookie.split('; ').find((entry) => entry.startsWith('XSRF-TOKEN='));

  return cookie ? decodeURIComponent(cookie.slice('XSRF-TOKEN='.length)) : '';
}

/** Die erste Seite öffnet die Verbindung, die letzte schließt sie wieder. */
let openConnections = 0;

export type EchoConnection = ReturnType<typeof echo<'pusher'>>;

/**
 * Die einzige Echo-Anbindung der Inertia-App (`@laravel/echo-vue`, Pusher). Sie verbindet sich beim
 * Aufruf in `setup` und trennt sich, sobald die Seite verlassen wird; Seiten ohne Echo laden weder
 * Pusher noch öffnen sie einen Socket. `null`, wenn kein Pusher-Key konfiguriert ist (z. B. lokal
 * ohne Pusher).
 *
 * Den Token für `/broadcasting/auth` liest sie aus dem `XSRF-TOKEN`-Cookie, weil das Inertia-Layout
 * kein `csrf-token`-Meta-Tag hat. Eigene Inertia-Requests tragen die Socket-ID, damit ein Push mit
 * `toOthers()` nicht zum Absender zurückkommt.
 */
export function useEchoConnection(): EchoConnection | null {
  const key = import.meta.env.VITE_PUSHER_APP_KEY as string | undefined;
  if (!key) {
    return null;
  }

  if (openConnections === 0) {
    configureEcho({
      broadcaster: 'pusher',
      key,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER as string,
      forceTLS: true,
      auth: { headers: { 'X-XSRF-TOKEN': xsrfToken() } },
    });
  }
  openConnections++;

  const connection = echo<'pusher'>();

  const removeSocketHeader = router.on('before', (event) => {
    const socketId = connection.socketId();
    if (socketId) {
      event.detail.visit.headers['X-Socket-ID'] = socketId;
    }
  });

  onUnmounted(() => {
    removeSocketHeader();
    openConnections--;
    if (openConnections === 0) {
      connection.disconnect();
    }
  });

  return connection;
}
