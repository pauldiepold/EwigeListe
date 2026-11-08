import { computed, type ComputedRef } from 'vue';
import { useEchoPresence } from '@laravel/echo-vue';
import { useEchoConnection, usePresenceMembers } from '@/composables/echoConnection';
import { useRefreshOnVisible } from '@/composables/useRefreshOnVisible';

type PresenceMember = { id: number };

/**
 * Hält eine Runde aktuell: Echo verbindet sich nur, solange die Seite offen ist, und meldet
 * Änderungen anderer Handys. Ein gesperrtes Handy verpasst Pushes, deshalb wird auch beim
 * Zurückkehren auf die Seite neu geladen.
 */
export function useRoundUpdates(
  roundId: number,
  refresh: () => void,
): { onlinePlayerIds: ComputedRef<number[]> } {
  useRefreshOnVisible(refresh);

  if (useEchoConnection() === null) {
    return { onlinePlayerIds: computed(() => []) };
  }

  const channel = useEchoPresence<unknown, 'pusher'>(
    `round.${roundId}`,
    'RoundUpdated',
    refresh,
  ).channel();
  const members = usePresenceMembers<PresenceMember>(channel);

  return { onlinePlayerIds: computed(() => members.value.map((member) => member.id)) };
}
