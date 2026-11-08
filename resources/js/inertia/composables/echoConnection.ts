import { onUnmounted, ref, type Ref } from 'vue';
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

/** Der Teil des Pusher-Channels, den `usePresenceMembers` braucht (Echo kapselt ihn in `subscription`). */
type PusherPresenceSubscription = {
  subscribed: boolean;
  members: { each: (callback: (member: { info: unknown }) => void) => void };
  bind: (event: string, callback: (payload: never) => void) => unknown;
  unbind: (event: string, callback: (payload: never) => void) => unknown;
};

export type PresenceChannel = ReturnType<EchoConnection['join']>;

/**
 * Die Mitgliederliste eines Presence-Channels, auch wenn der Channel schon offen ist.
 *
 * Echo zählt Abonnements pro Channel-Name: Wechselt der Nutzer von der Runde zur Live-Seite (oder
 * zurück), baut Inertia die neue Seite auf, bevor die alte abgebaut ist, und `useEchoPresence`
 * gibt den schon abonnierten Channel zurück. `here()` feuert aber nur bei
 * `pusher:subscription_succeeded` und käme dann nie. Deshalb liest diese Funktion die Mitglieder
 * sofort aus dem Pusher-Channel, sobald er abonniert ist, und folgt danach `member_added` und
 * `member_removed`. Die Listener hängen direkt am Pusher-Channel und werden mit der Seite wieder
 * gelöst, damit sie sich auf dem geteilten Channel nicht stapeln.
 *
 * @param onLeaving wird zusätzlich aufgerufen, wenn jemand den Channel verlässt
 */
export function usePresenceMembers<T extends { id: number }>(
  channel: PresenceChannel,
  onLeaving?: (member: T) => void,
): Ref<T[]> {
  const subscription = (channel as unknown as { subscription: PusherPresenceSubscription })
    .subscription;
  const members = ref<T[]>([]) as Ref<T[]>;

  const readCurrentMembers = (): void => {
    const current: T[] = [];
    subscription.members.each((member) => current.push(member.info as T));
    members.value = current;
  };
  const addMember = (member: { info: T }): void => {
    if (!members.value.some((online) => online.id === member.info.id)) {
      members.value = [...members.value, member.info];
    }
  };
  const removeMember = (member: { info: T }): void => {
    members.value = members.value.filter((online) => online.id !== member.info.id);
    onLeaving?.(member.info);
  };

  subscription.bind('pusher:subscription_succeeded', readCurrentMembers);
  subscription.bind('pusher:member_added', addMember);
  subscription.bind('pusher:member_removed', removeMember);

  if (subscription.subscribed) {
    readCurrentMembers();
  }

  onUnmounted(() => {
    subscription.unbind('pusher:subscription_succeeded', readCurrentMembers);
    subscription.unbind('pusher:member_added', addMember);
    subscription.unbind('pusher:member_removed', removeMember);
  });

  return members;
}
