<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LiveLayout from '@/Layouts/LiveLayout.vue';
import LiveTable from '@/Components/Live/LiveTable.vue';
import LiveToolbar from '@/Components/Live/LiveToolbar.vue';
import { useLiveGameState } from '@/composables/useLiveGameState';
import type { LiveShowProps } from '@/types/live';

const props = defineProps<LiveShowProps>();

/**
 * Der Live-Zustand an einer Stelle: Er startet mit den Props und wird später von Pushes
 * (`state.applyPush`) fortgeschrieben. Hand, Stich, Overlays und Meldungen hängen als eigene
 * Komponenten in den Slots des Tisches und lesen von hier.
 */
const state = useLiveGameState(props);
</script>

<template>
  <Head title="Live-Spiel" />

  <LiveLayout>
    <!-- Hochformat: Der Tisch braucht die Breite. `portrait:` und `landscape:` folgen der Ausrichtung des Fensters. -->
    <div class="hidden h-full flex-col items-center justify-center gap-4 text-center portrait:flex">
      <UIcon name="i-lucide-smartphone" class="size-12 rotate-90 text-muted" aria-hidden="true" />
      <p class="text-lg font-semibold">Bitte drehe dein Gerät</p>
      <UButton :to="round.path" color="neutral" variant="outline" label="Zurück zur Runde" />
    </div>

    <LiveTable
      class="portrait:hidden"
      :players="round.players"
      :viewer-player-id="round.viewerPlayerId"
      :live-game="state.liveGame.value"
    >
      <template #tools>
        <LiveToolbar :back-path="round.path" :rules="round.rules" />
      </template>
    </LiveTable>
  </LiveLayout>
</template>
