<script setup lang="ts">
import { useAppRoute } from '@/composables/useAppRoute';
import type { GroupIndexRow } from '@/types/groups-index';

defineProps<{
  groups: GroupIndexRow[];
}>();

const { route } = useAppRoute();
</script>

<template>
  <div class="mx-auto w-full max-w-md space-y-2">
    <UButton
      v-for="group in groups"
      :key="group.id"
      :to="route('groups.show', { group: group.id })"
      color="neutral"
      variant="ghost"
      block
      class="flex h-auto w-full flex-row items-center justify-between px-3 py-3 text-default hover:bg-muted"
    >
      <span class="truncate font-semibold text-highlighted">{{ group.name }}</span>
      <span class="ml-2 flex shrink-0 items-center gap-3 text-sm text-muted">
        <UIcon v-if="group.closed" name="i-lucide-lock" class="size-4" aria-hidden="true" />
        <span>Personen: {{ group.players_count }}</span>
        <span>Runden: {{ group.rounds_count }}</span>
      </span>
    </UButton>
  </div>
</template>
