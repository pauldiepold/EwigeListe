<script setup lang="ts">
import type { LiveEchoMember } from '@/composables/useLiveEcho';

defineProps<{
  /** Alle, die online sind, aber nicht mitspielen (auch wer in diesem Spiel aussetzt). */
  spectators: LiveEchoMember[];
}>();
</script>

<template>
  <ul
    v-if="spectators.length > 0"
    aria-label="Zuschauer"
    class="flex gap-1.5 rounded-lg bg-default/90 p-1.5 ring ring-default"
  >
    <li v-for="spectator in spectators" :key="spectator.id" :aria-label="spectator.name">
      <UAvatar :src="spectator.avatar_path ?? undefined" :alt="spectator.name" size="sm" />
    </li>
  </ul>
</template>
