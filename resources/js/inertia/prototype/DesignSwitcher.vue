<script setup lang="ts">
/** PROTOTYP #34, Wegwerf-Code: siehe designSpike.ts. */
import { onMounted, onUnmounted } from 'vue';
import { useDesignSpike } from './designSpike';

const { currentDesign, cycle } = useDesignSpike();

function onKeydown(event: KeyboardEvent): void {
  const target = event.target as HTMLElement;
  if (target.closest('input, textarea, [contenteditable]')) {
    return;
  }

  if (event.key === 'ArrowLeft') {
    cycle(-1);
  } else if (event.key === 'ArrowRight') {
    cycle(1);
  }
}

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => window.removeEventListener('keydown', onKeydown));
</script>

<template>
  <div
    class="absolute top-1 right-1 flex items-center rounded-full bg-black text-yellow-300 shadow-lg ring-1 ring-yellow-300/70"
    data-design-switcher
  >
    <button
      type="button"
      class="cursor-pointer px-2 py-1.5"
      aria-label="Vorherige Variante"
      @click="cycle(-1)"
    >
      &lsaquo;
    </button>
    <span class="flex min-w-14 flex-col items-center text-center leading-none">
      <span class="text-xs font-bold uppercase">{{ currentDesign.key }}</span>
      <span class="text-[10px]">{{ currentDesign.name }}</span>
    </span>
    <button
      type="button"
      class="cursor-pointer px-2 py-1.5"
      aria-label="Nächste Variante"
      @click="cycle(1)"
    >
      &rsaquo;
    </button>
  </div>
</template>
