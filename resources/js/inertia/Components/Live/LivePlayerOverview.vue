<script setup lang="ts">
import { computed } from 'vue';
import type { LivePlayer } from '@/types/live';

const props = defineProps<{
  /** Die Spieler des (nächsten) Spiels. */
  players: LivePlayer[];
  onlinePlayerIds: number[];
  /** Zwischen zwei Spielen: zeigt „bereit“ und den Bereit-Button. Mitten im Spiel fehlt beides. */
  showReady?: boolean;
  /** Spieler, die „bereit“ gemeldet haben, unabhängig davon, ob sie gerade online sind. */
  readyIds?: number[];
  /** Davor wurde noch kein Spiel gespielt. */
  isFirstGame?: boolean;
  /** Ich spiele mit und kann „bereit“ melden; wer aussetzt oder zuschaut, sieht nur zu. */
  canBeReady?: boolean;
  iAmReady?: boolean;
}>();

defineEmits<{ ready: [] }>();

const allOnline = computed(() =>
  props.players.every((player) => props.onlinePlayerIds.includes(player.id)),
);

function isOnline(player: LivePlayer): boolean {
  return props.onlinePlayerIds.includes(player.id);
}

function isReady(player: LivePlayer): boolean {
  return props.showReady === true && (props.readyIds ?? []).includes(player.id);
}

function label(player: LivePlayer): string {
  return [player.firstName, isOnline(player) ? 'online' : 'offline', isReady(player) && 'bereit']
    .filter(Boolean)
    .join(', ');
}
</script>

<template>
  <div class="flex flex-col items-center gap-5 rounded-lg bg-default px-6 py-5 ring ring-default">
    <UButton
      v-if="showReady && canBeReady && !iAmReady"
      label="Bereit?"
      :disabled="!allOnline"
      @click="$emit('ready')"
    />
    <p class="text-center font-semibold">
      <template v-if="!allOnline">Bitte warte, bis alle online sind</template>
      <template v-else-if="showReady">
        {{ isFirstGame ? 'Das erste' : 'Das nächste' }} Spiel startet, sobald alle bereit sind
      </template>
    </p>
    <!-- Abstand so groß, dass sich die Leucht-Schatten der Avatare nicht berühren; am Desktop alle in einer Reihe. -->
    <ul class="grid grid-cols-2 gap-x-10 gap-y-6 lg:grid-cols-4 lg:gap-x-12">
      <li
        v-for="player in players"
        :key="player.id"
        :aria-label="label(player)"
        class="flex items-center gap-3"
      >
        <UAvatar
          :src="player.avatarPath ?? undefined"
          :alt="player.firstName"
          size="md"
          class="transition-shadow"
          :class="isOnline(player) ? 'shadow-[0_0_10px_2px] shadow-success/60' : 'opacity-40'"
        />
        <div class="flex min-w-0 flex-col">
          <span class="truncate text-sm leading-5">{{ player.firstName }}</span>
          <span v-if="isReady(player)" class="text-xs leading-4 text-success/70">bereit</span>
        </div>
      </li>
    </ul>
  </div>
</template>
