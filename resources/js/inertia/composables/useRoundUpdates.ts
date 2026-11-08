import { ref, type Ref } from 'vue';
import { useEchoPresence } from '@laravel/echo-vue';
import { useEchoConnection } from '@/composables/echoConnection';
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
): { onlinePlayerIds: Ref<number[]> } {
  const onlinePlayerIds = ref<number[]>([]);

  useRefreshOnVisible(refresh);

  if (useEchoConnection() === null) {
    return { onlinePlayerIds };
  }

  useEchoPresence<unknown, 'pusher'>(`round.${roundId}`, 'RoundUpdated', refresh)
    .channel()
    .here((members: PresenceMember[]) => {
      onlinePlayerIds.value = members.map((member) => member.id);
    })
    .joining((member: PresenceMember) => {
      onlinePlayerIds.value = [...onlinePlayerIds.value, member.id];
    })
    .leaving((member: PresenceMember) => {
      onlinePlayerIds.value = onlinePlayerIds.value.filter((id) => id !== member.id);
    });

  return { onlinePlayerIds };
}
