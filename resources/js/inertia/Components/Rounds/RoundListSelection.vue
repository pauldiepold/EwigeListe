<script setup lang="ts">
import { computed } from 'vue';
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

const summary = computed(() => {
  if (props.selectedCount === 1) {
    return 'einer Liste';
  }
  return `${props.selectedCount} Listen`;
});
</script>

<template>
  <UCard>
    <template #header>
      <h2 class="text-lg font-semibold">Listen</h2>
      <p class="text-sm text-muted">Runde wird {{ summary }} hinzugefügt.</p>
    </template>

    <div class="space-y-3">
      <USwitch
        v-for="list in lists"
        :key="list.id"
        :model-value="isSelected(list.id)"
        :disabled="isLocked(list.id)"
        :label="list.name"
        color="primary"
        @update:model-value="(value: boolean) => emit('toggle', list.id, value)"
      />
    </div>
  </UCard>
</template>
