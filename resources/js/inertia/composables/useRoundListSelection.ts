import { computed, ref, type Ref } from 'vue';
import type { CreateRoundGroup, CreateRoundPlayer } from '@/types/round-create';

export const EWIGE_LISTE_ID = 1;

/**
 * Listenauswahl beim Erstellen einer Runde.
 *
 * Eine Liste ist ausgewählt, wenn sie die Ewige Liste ist, manuell angeschaltet wurde
 * oder Standardliste eines gewählten Spielers ist und nicht manuell ausgeschaltet wurde.
 * Wird ein Spieler hinzugefügt, gelten seine Standardlisten wieder als angeschaltet.
 */
export function useRoundListSelection(selectedPlayers: Ref<CreateRoundPlayer[]>) {
  const manuallySelected = ref<number[]>([]);
  const manuallyDeselected = ref<number[]>([]);

  const selectableGroups = computed<CreateRoundGroup[]>(() => {
    const groups: CreateRoundGroup[] = [];
    for (const player of selectedPlayers.value) {
      for (const group of player.groups) {
        if (group.closed !== 1 && !groups.some((entry) => entry.id === group.id)) {
          groups.push(group);
        }
      }
    }
    return groups.sort((a, b) => Number(b.id === EWIGE_LISTE_ID) - Number(a.id === EWIGE_LISTE_ID));
  });

  const standardGroupIds = computed(() => {
    return new Set(
      selectedPlayers.value.flatMap((player) =>
        player.profiles.filter((profile) => profile.default).map((profile) => profile.group_id),
      ),
    );
  });

  function isSelected(groupId: number): boolean {
    if (groupId === EWIGE_LISTE_ID || manuallySelected.value.includes(groupId)) {
      return true;
    }
    return !manuallyDeselected.value.includes(groupId) && standardGroupIds.value.has(groupId);
  }

  const selectedGroupIds = computed(() =>
    selectableGroups.value.filter((group) => isSelected(group.id)).map((group) => group.id),
  );

  function setSelected(groupId: number, selected: boolean): void {
    if (groupId === EWIGE_LISTE_ID) {
      return;
    }
    manuallySelected.value = manuallySelected.value.filter((id) => id !== groupId);
    manuallyDeselected.value = manuallyDeselected.value.filter((id) => id !== groupId);
    if (selected) {
      manuallySelected.value.push(groupId);
    } else {
      manuallyDeselected.value.push(groupId);
    }
  }

  function resetStandardGroupsOf(player: CreateRoundPlayer): void {
    const playerStandardIds = player.profiles
      .filter((profile) => profile.default)
      .map((profile) => profile.group_id);
    manuallyDeselected.value = manuallyDeselected.value.filter(
      (id) => !playerStandardIds.includes(id),
    );
  }

  return { selectableGroups, selectedGroupIds, isSelected, setSelected, resetStandardGroupsOf };
}
