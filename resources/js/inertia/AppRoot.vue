<script setup lang="ts">
/**
 * UApp stellt Toasts, Tooltips & Co. bereit; liegt über den Pages, damit es Seitenwechsel überlebt.
 * Toasts oben mittig: unten gehen sie am Handy unter.
 */
import FlashToasts from '@/Components/Ui/FlashToasts.vue';
</script>

<template>
  <UApp :toaster="{ position: 'top-center' }">
    <slot />
    <FlashToasts />
  </UApp>
</template>
