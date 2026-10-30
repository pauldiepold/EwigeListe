<script setup lang="ts">
/**
 * UApp stellt Toasts, Tooltips & Co. bereit; liegt über den Pages, damit es Seitenwechsel überlebt.
 */
import FlashToasts from '@/Components/Ui/FlashToasts.vue';
</script>

<template>
  <UApp>
    <slot />
    <FlashToasts />
  </UApp>
</template>
