<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { BadgeType, GroupBadge } from '@/types/groups-show';

const props = defineProps<{
  badge: GroupBadge;
  type: BadgeType;
}>();

const typeLabel = computed(() => (props.type === 'points' ? 'Meiste Punkte' : 'Meiste Spiele'));
</script>

<template>
  <Link
    :href="badge.path"
    class="mx-auto flex w-64 items-center gap-4 rounded-lg bg-yellow-400 p-2 text-black no-underline shadow"
  >
    <div class="flex flex-1 flex-col items-center">
      <UIcon name="i-lucide-crown" class="size-5" aria-hidden="true" />
      <span class="text-3xl font-bold">{{ badge.value }}</span>
      <span class="text-xs text-gray-700">{{ typeLabel }}</span>
    </div>
    <div class="flex flex-2 flex-col items-center">
      <span class="text-2xl">{{ badge.name }}</span>
      <span class="text-xs text-gray-700">{{ badge.date }}</span>
    </div>
  </Link>
</template>
