<script setup lang="ts">
import { computed, watch } from 'vue';
import type { LiveMessage, LivePlayer } from '@/types/live';

const props = defineProps<{
  /** Neueste zuerst, wie die Engine sie liefert. */
  messages: LiveMessage[];
  /** Alle Spieler der Runde; der Name vor dem Text kommt von hier. */
  players: LivePlayer[];
}>();

/** Ob das Protokoll aufgeklappt ist; eine neue Meldung klappt es auf. */
const open = defineModel<boolean>('open', { default: false });

const items = computed(() =>
  props.messages.map((message, index) => ({
    // Neue Meldungen kommen vorn dazu, vom Ende her gezählt bleibt der Schlüssel stabil.
    key: props.messages.length - index,
    name: props.players.find((player) => player.id === message.spielerId)?.firstName ?? null,
    text: message.text,
  })),
);

/** Ändert sich, wenn eine Meldung dazukommt; ein Neuladen mit demselben Inhalt lässt ihn gleich. */
const newestMessage = computed(() => {
  const newest = props.messages[0];

  return newest === undefined
    ? null
    : `${props.messages.length}|${newest.spielerId ?? ''}|${newest.text}`;
});

watch(newestMessage, (next) => {
  if (next !== null) {
    open.value = true;
  }
});
</script>

<template>
  <section
    v-if="open"
    aria-label="Meldungen"
    class="flex max-h-36 w-56 flex-col rounded-lg bg-default/90 text-xs ring ring-default"
  >
    <header class="flex items-center justify-between gap-2 px-2 pt-1">
      <h2 class="font-semibold text-highlighted">Meldungen</h2>
      <UButton
        icon="i-lucide-x"
        color="neutral"
        variant="ghost"
        size="xs"
        aria-label="Meldungen ausblenden"
        @click="open = false"
      />
    </header>
    <p v-if="items.length === 0" class="px-2 pb-2 text-muted">Noch keine Meldungen.</p>
    <ul v-else class="space-y-1 overflow-y-auto px-2 pb-2">
      <li v-for="item in items" :key="item.key">
        <b v-if="item.name !== null" class="text-highlighted">{{ `${item.name} ` }}</b
        >{{ item.text }}
      </li>
    </ul>
  </section>
</template>
