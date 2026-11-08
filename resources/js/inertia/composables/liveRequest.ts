import { http } from '@inertiajs/vue3';

/**
 * Sendet eine Aktion an die Live-Engine. Die Endpunkte antworten mit Text (`success`), nicht mit
 * einer Inertia-Antwort oder JSON; deshalb der HTTP-Client von Inertia statt `router.post` (der
 * Fehlerdialog für „keine Inertia-Antwort“ würde aufgehen). Den `XSRF-TOKEN` hängt der Client
 * selbst an. Schlägt die Aktion fehl (z. B. 422), wirft der Aufruf.
 */
export async function postLive(url: string, data?: Record<string, unknown>): Promise<void> {
  await http.getClient().request({
    method: 'post',
    url,
    data,
    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
  });
}
