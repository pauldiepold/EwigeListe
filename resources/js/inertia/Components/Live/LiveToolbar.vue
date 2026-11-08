<script setup lang="ts">
import { ref } from 'vue';
import LiveRulesList from '@/Components/Live/LiveRulesList.vue';
import { useFullscreen } from '@/composables/useFullscreen';
import type { LiveRules } from '@/Components/Live/liveRules';

defineProps<{
  /** Adresse der Runden-Seite. */
  backPath: string;
  rules: LiveRules;
}>();

defineSlots<{
  /** Weitere Werkzeuge unter den festen (letzter Stich, Meldungen, Neu laden, …). */
  default?: () => unknown;
}>();

const { isSupported: canFullscreen, isFullscreen, toggle: toggleFullscreen } = useFullscreen();

/** Ob das Meldungsprotokoll aufgeklappt ist (geteilt mit `LiveMessages`). */
const messagesOpen = defineModel<boolean>('messagesOpen', { default: false });

const infoOpen = ref(false);
</script>

<template>
  <div
    class="flex flex-col gap-2 rounded-lg bg-default/90 p-1.5 ring ring-default"
    role="toolbar"
    aria-label="Werkzeuge"
    aria-orientation="vertical"
  >
    <UButton
      :to="backPath"
      icon="i-lucide-arrow-left"
      color="neutral"
      variant="ghost"
      aria-label="Zurück zur Runde"
      title="Zurück zur Runde"
    />
    <UButton
      icon="i-lucide-info"
      color="neutral"
      variant="ghost"
      aria-label="Info"
      title="Info und Regeln"
      @click="infoOpen = true"
    />
    <UButton
      icon="i-lucide-message-square-text"
      color="neutral"
      :variant="messagesOpen ? 'soft' : 'ghost'"
      aria-label="Meldungen"
      title="Meldungen ein- und ausblenden"
      :aria-pressed="messagesOpen"
      @click="messagesOpen = !messagesOpen"
    />
    <UButton
      v-if="canFullscreen"
      :icon="isFullscreen ? 'i-lucide-minimize' : 'i-lucide-maximize'"
      color="neutral"
      variant="ghost"
      :aria-label="isFullscreen ? 'Vollbild beenden' : 'Vollbild'"
      :title="isFullscreen ? 'Vollbild beenden' : 'Vollbild'"
      @click="toggleFullscreen"
    />
    <slot />

    <UModal
      v-model:open="infoOpen"
      title="Regeln dieser Runde"
      description="Sie gelten für alle Spiele dieser Runde."
    >
      <template #body>
        <LiveRulesList :rules="rules" />
      </template>
    </UModal>
  </div>
</template>
