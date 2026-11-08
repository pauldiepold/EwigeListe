<script setup lang="ts">
import type { LivePlayer } from '@/types/live';

defineProps<{
  players: LivePlayer[];
  /** Spieler mit Haken: online beim Warten, bereit beim Bereit-Check. */
  checkedIds: number[];
  checkedLabel: string;
  uncheckedLabel: string;
}>();
</script>

<template>
  <ul class="grid grid-cols-2 gap-x-4 gap-y-2">
    <li
      v-for="player in players"
      :key="player.id"
      :aria-label="`${player.firstName}, ${checkedIds.includes(player.id) ? checkedLabel : uncheckedLabel}`"
      class="flex items-center gap-2"
    >
      <UAvatar
        :src="player.avatarPath ?? undefined"
        :alt="player.firstName"
        size="md"
        class="ring-2 transition-shadow"
        :class="checkedIds.includes(player.id) ? 'ring-success' : 'opacity-60 ring-transparent'"
      />
      <span class="truncate text-sm">{{ player.firstName }}</span>
    </li>
  </ul>
</template>
