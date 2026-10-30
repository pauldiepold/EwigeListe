<script setup lang="ts">
import FlashToasts from '@/Components/Ui/FlashToasts.vue';
</script>

<template>
  <!-- UApp stellt Toasts, Tooltips & Co. bereit; liegt über den Pages, damit es Seitenwechsel überlebt. -->
  <UApp>
    <slot />
    <FlashToasts />
  </UApp>
</template>
