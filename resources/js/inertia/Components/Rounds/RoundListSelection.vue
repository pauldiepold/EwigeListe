<script setup lang="ts">
import { computed, useId } from 'vue';
import type { CreateRoundList } from '@/types/round-create';

const props = defineProps<{
  lists: Pick<CreateRoundList, 'id' | 'name'>[];
  isSelected: (listId: number) => boolean;
  isLocked: (listId: number) => boolean;
  selectedCount: number;
}>();

const emit = defineEmits<{
  toggle: [listId: number, selected: boolean];
}>();

const idPrefix = useId();

const summary = computed(() =>
  props.selectedCount === 1 ? 'eine Liste' : `${props.selectedCount} Listen`,
);
</script>

<template>
  <section class="space-y-2">
    <h2 class="text-center text-muted">Runde zählt für {{ summary }}:</h2>

    <!-- Gesperrte Listen (Ewige Liste, geschlossene Listen) lassen sich nicht abwählen:
         Schloss statt Schalter. Abgewählte Listen treten grau zurück. -->
    <ul class="divide-y divide-default rounded-lg border border-default bg-default">
      <li
        v-for="list in lists"
        :key="list.id"
        class="flex min-h-11 items-center justify-between gap-3 px-3"
      >
        <span
          :id="`${idPrefix}-${list.id}`"
          class="truncate font-medium"
          :class="isSelected(list.id) ? 'text-highlighted' : 'text-dimmed'"
        >
          {{ list.name }}
        </span>
        <UIcon
          v-if="isLocked(list.id)"
          name="i-lucide-lock"
          class="size-4 shrink-0 text-dimmed"
          role="img"
          aria-label="Immer dabei"
        />
        <USwitch
          v-else
          :model-value="isSelected(list.id)"
          :aria-labelledby="`${idPrefix}-${list.id}`"
          color="primary"
          @update:model-value="(value: boolean) => emit('toggle', list.id, value)"
        />
      </li>
    </ul>
  </section>
</template>
