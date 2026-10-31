<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { index as home } from '@/routes';

const props = defineProps<{
  status: number;
}>();

const messages: Record<number, { title: string; description: string }> = {
  401: { title: 'Nicht angemeldet', description: 'Bitte melde dich an, um diese Seite zu sehen.' },
  403: { title: 'Kein Zugriff', description: 'Diese Seite ist für dich nicht freigegeben.' },
  404: { title: 'Seite nicht gefunden', description: 'Diese Seite gibt es nicht (mehr).' },
  429: {
    title: 'Zu viele Anfragen',
    description: 'Warte bitte einen Moment und versuche es dann noch einmal.',
  },
};

const fallback = {
  title: 'Da ist etwas schiefgelaufen',
  description: 'Versuche es gleich noch einmal.',
};

const message = computed(() => messages[props.status] ?? fallback);

// Ab 500 kommen bewusst keine geteilten Daten mit, die Navigation hätte nichts anzuzeigen.
const hasNavigation = computed(() => props.status < 500);
</script>

<template>
  <Head :title="message.title" />

  <component :is="hasNavigation ? AppLayout : 'main'">
    <div
      class="mx-auto flex max-w-md flex-col items-center gap-4 p-4 text-center"
      :class="{ 'min-h-screen justify-center': !hasNavigation }"
    >
      <p class="text-5xl font-semibold text-muted">{{ status }}</p>
      <h1 class="text-xl font-semibold text-highlighted">{{ message.title }}</h1>
      <p class="text-muted">{{ message.description }}</p>
      <UButton :to="home.url()" color="primary" icon="i-lucide-house" label="Zur Startseite" />
    </div>
  </component>
</template>
