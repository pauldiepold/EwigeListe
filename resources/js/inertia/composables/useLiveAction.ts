import { ref, type Ref } from 'vue';
import { postLive } from '@/composables/liveRequest';
import { reloadPage } from '@/composables/reloadPage';

export type LiveAction = {
  /** Eine Aktion ist unterwegs; bis zur Antwort sollten keine weiteren gesendet werden. */
  busy: Ref<boolean>;
  /** Sendet die Aktion und fängt Fehler ab. `true`, wenn die Engine sie angenommen hat. */
  send: (url: string, data?: Record<string, unknown>) => Promise<boolean>;
};

/** Die Meldung der Engine aus einer 422-Antwort (`{"message": "…"}`), falls sie eine mitschickt. */
function messageOf(data: unknown): string | null {
  try {
    const body = typeof data === 'string' ? JSON.parse(data) : data;

    return typeof body?.message === 'string' && body.message !== '' ? body.message : null;
  } catch {
    return null;
  }
}

/**
 * Aktionen des Spielers an die Live-Engine (Vorbehalt, Karte spielen, Armut, Ansage) mit der
 * gemeinsamen Fehlerbehandlung:
 *
 * - 422: Die Engine hat die Aktion abgelehnt (falsche Phase, nicht dran, Karte nicht spielbar), meist
 *   weil die Seite veraltet ist. Toast mit der Meldung der Engine, dann `reloadData`.
 * - 419: Die Sitzung ist abgelaufen. Die Seite lädt komplett neu.
 * - sonst: Toast mit einer allgemeinen Meldung.
 */
export function useLiveAction(reloadData: () => void): LiveAction {
  const toast = useToast();
  const busy = ref(false);

  async function send(url: string, data?: Record<string, unknown>): Promise<boolean> {
    busy.value = true;

    try {
      await postLive(url, data);

      return true;
    } catch (error) {
      const response = (error as { response?: { status?: number; data?: unknown } })?.response;

      if (response?.status === 419) {
        reloadPage();
      } else if (response?.status === 422) {
        toast.add({
          title: messageOf(response.data) ?? 'Das war gerade nicht möglich',
          color: 'error',
        });
        reloadData();
      } else {
        toast.add({ title: 'Die Aktion konnte nicht gesendet werden', color: 'error' });
      }

      return false;
    } finally {
      busy.value = false;
    }
  }

  return { busy, send };
}
