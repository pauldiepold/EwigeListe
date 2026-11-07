import { computed, ref, type Ref } from 'vue';
import type { CreateRoundList, CreateRoundPlayer } from '@/types/round-create';

export const EWIGE_LISTE: CreateRoundList = { id: 1, name: 'Ewige Liste', closed: false };

function standardListIdsOf(player: CreateRoundPlayer): number[] {
  return player.profiles.filter((profile) => profile.default).map((profile) => profile.group_id);
}

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

  function isLocked(listId: number): boolean {
    return listId === EWIGE_LISTE.id;
  }

  const selectableLists = computed<CreateRoundList[]>(() => {
    const lists: CreateRoundList[] = [EWIGE_LISTE];
    for (const player of selectedPlayers.value) {
      for (const list of player.groups) {
        if (!list.closed && !lists.some((entry) => entry.id === list.id)) {
          lists.push(list);
        }
      }
    }
    return lists;
  });

  const standardListIds = computed(
    () => new Set(selectedPlayers.value.flatMap((player) => standardListIdsOf(player))),
  );

  function isSelected(listId: number): boolean {
    if (isLocked(listId) || manuallySelected.value.includes(listId)) {
      return true;
    }
    return !manuallyDeselected.value.includes(listId) && standardListIds.value.has(listId);
  }

  const selectedListIds = computed(() =>
    selectableLists.value.filter((list) => isSelected(list.id)).map((list) => list.id),
  );

  function setSelected(listId: number, selected: boolean): void {
    if (isLocked(listId)) {
      return;
    }
    manuallySelected.value = manuallySelected.value.filter((id) => id !== listId);
    manuallyDeselected.value = manuallyDeselected.value.filter((id) => id !== listId);
    (selected ? manuallySelected : manuallyDeselected).value.push(listId);
  }

  function reselectStandardListsOf(player: CreateRoundPlayer): void {
    const ids = standardListIdsOf(player);
    manuallyDeselected.value = manuallyDeselected.value.filter((id) => !ids.includes(id));
  }

  return {
    selectableLists,
    selectedListIds,
    isSelected,
    isLocked,
    setSelected,
    reselectStandardListsOf,
  };
}
