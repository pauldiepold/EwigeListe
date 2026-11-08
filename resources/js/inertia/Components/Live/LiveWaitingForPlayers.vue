<script setup lang="ts">
import LivePlayerChecklist from '@/Components/Live/LivePlayerChecklist.vue';
import type { LivePlayer } from '@/types/live';

defineProps<{
  /** Die Spieler des (nächsten) Spiels. */
  players: LivePlayer[];
  onlinePlayerIds: number[];
}>();
</script>

<template>
  <div class="flex flex-col gap-3 rounded-lg bg-default px-4 py-3 ring ring-default">
    <p class="text-center font-semibold">Bitte warte, bis alle online sind</p>
    <LivePlayerChecklist
      :players="players"
      :checked-ids="onlinePlayerIds"
      checked-label="online"
      unchecked-label="offline"
    />
  </div>
</template>
