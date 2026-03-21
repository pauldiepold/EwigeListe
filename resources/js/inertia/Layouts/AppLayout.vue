<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

defineProps<{
  title?: string;
}>();

type ColorMode = 'light' | 'dark';

const colorMode = ref<ColorMode>('light');

const isDark = computed(() => colorMode.value === 'dark');

function applyColorMode(mode: ColorMode): void {
  colorMode.value = mode;
  const root = document.documentElement;
  root.classList.toggle('dark', mode === 'dark');
  localStorage.setItem('ui-color-mode', mode);
}

function toggleColorMode(): void {
  applyColorMode(colorMode.value === 'dark' ? 'light' : 'dark');
}

onMounted(() => {
  const stored = localStorage.getItem('ui-color-mode');
  if (stored === 'dark' || stored === 'light') {
    applyColorMode(stored);
    return;
  }

  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyColorMode(prefersDark ? 'dark' : 'light');
});
</script>

<template>
  <div class="min-h-screen bg-default text-highlighted">
    <main class="mx-auto max-w-4xl px-4 py-8">
      <header class="mb-6 flex items-center justify-between border-b border-default pb-3">
        <h1 class="text-2xl font-semibold">
          {{ title ?? 'Ewige Liste' }}
        </h1>
        <UButton
          color="neutral"
          variant="ghost"
          :icon="isDark ? 'i-lucide-sun' : 'i-lucide-moon'"
          :label="isDark ? 'Hell' : 'Dunkel'"
          @click="toggleColorMode"
        />
      </header>
      <slot />
    </main>
  </div>
</template>
