import { onMounted, onUnmounted, ref, type Ref } from 'vue';
import { router } from '@inertiajs/vue3';

type PresenceMember = { id: number };

function xsrfToken(): string {
  const cookie = document.cookie.split('; ').find((entry) => entry.startsWith('XSRF-TOKEN='));

  return cookie ? decodeURIComponent(cookie.slice('XSRF-TOKEN='.length)) : '';
}

/**
 * Hält eine Runde aktuell: Echo verbindet sich nur, solange die Seite offen ist, und meldet
 * Änderungen anderer Handys. Ein gesperrtes Handy verpasst Pushes, deshalb wird auch beim
 * Zurückkehren auf die Seite neu geladen.
 */
export function useRoundUpdates(
  roundId: number,
  refresh: () => void,
): { onlinePlayerIds: Ref<number[]> } {
  const onlinePlayerIds = ref<number[]>([]);
  let leaveChannel: (() => void) | null = null;
  let stopped = false;

  function refreshWhenVisible(): void {
    if (document.visibilityState === 'visible') {
      refresh();
    }
  }

  async function connect(): Promise<void> {
    const key = import.meta.env.VITE_PUSHER_APP_KEY as string | undefined;
    if (!key) {
      return;
    }

    const [{ default: Echo }, { default: Pusher }] = await Promise.all([
      import('laravel-echo'),
      import('pusher-js'),
    ]);
    if (stopped) {
      return;
    }

    const echo = new Echo({
      broadcaster: 'pusher',
      Pusher,
      key,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER as string,
      forceTLS: true,
      auth: { headers: { 'X-XSRF-TOKEN': xsrfToken() } },
    });

    const channelName = `round.${roundId}`;
    echo
      .join(channelName)
      .here((members: PresenceMember[]) => {
        onlinePlayerIds.value = members.map((member) => member.id);
      })
      .joining((member: PresenceMember) => {
        onlinePlayerIds.value = [...onlinePlayerIds.value, member.id];
      })
      .leaving((member: PresenceMember) => {
        onlinePlayerIds.value = onlinePlayerIds.value.filter((id) => id !== member.id);
      })
      .listen('RoundUpdated', refresh);

    /** Ohne diesen Header bekäme das eintragende Handy seinen eigenen Push (`toOthers`). */
    const removeSocketHeader = router.on('before', (event) => {
      const socketId = echo.socketId();
      if (socketId) {
        event.detail.visit.headers['X-Socket-ID'] = socketId;
      }
    });

    leaveChannel = () => {
      removeSocketHeader();
      echo.leave(channelName);
      echo.disconnect();
    };
  }

  onMounted(() => {
    document.addEventListener('visibilitychange', refreshWhenVisible);
    void connect();
  });

  onUnmounted(() => {
    stopped = true;
    document.removeEventListener('visibilitychange', refreshWhenVisible);
    leaveChannel?.();
  });

  return { onlinePlayerIds };
}
