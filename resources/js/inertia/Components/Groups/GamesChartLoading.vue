<script setup lang="ts">
import AppLoadingIndicator from '@/Components/Ui/AppLoadingIndicator.vue';
</script>

<template>
  <div class="mx-auto max-w-3xl rounded border border-default bg-elevated p-4" aria-busy="true">
    <div class="flex min-h-[360px] flex-col items-center justify-center gap-2 rounded bg-muted/10">
      <AppLoadingIndicator label="Chart wird geladen…" size="md" />
    </div>
  </div>
</template>
