<script setup lang="ts">
import { onMounted } from 'vue';
import { resolveColorMode } from '@/composables/inertiaColorMode';
import { useWakeLock } from '@/composables/useWakeLock';

// Das Layout kennt keine Navigation und keinen Footer: Im Querformat am Handy soll der Tisch
// den ganzen Bildschirm bekommen. Solange es offen ist, bleibt der Bildschirm an.
useWakeLock();

// Wer die Live-Seite direkt öffnet, kommt nicht über `AppLayout`, das den Farbmodus setzt.
onMounted(() => {
  document.documentElement.classList.toggle('dark', resolveColorMode() === 'dark');
});
</script>

<template>
  <div
    class="isolate h-dvh bg-default pt-[env(safe-area-inset-top)] pr-[max(0.5rem,env(safe-area-inset-right))] pb-[max(0.5rem,env(safe-area-inset-bottom))] pl-[max(0.5rem,env(safe-area-inset-left))] text-highlighted"
  >
    <slot />
  </div>
</template>
