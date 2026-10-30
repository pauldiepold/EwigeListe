<script setup lang="ts">
import { onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import type { FlashToast } from '@/types/flash';

/**
 * Zeigt Inertia-Flash `toast` zentral an. Einmal an der App-Wurzel gemountet, damit der
 * Listener Seitenwechsel überlebt und auch die Meldung des ersten Seitenaufrufs erwischt.
 */
const toast = useToast();

function showToast(flashToast: FlashToast): void {
  const isError = flashToast.type === 'error';

  toast.add({
    title: flashToast.message,
    color: isError ? 'error' : 'success',
    icon: isError ? 'i-lucide-circle-alert' : 'i-lucide-check-circle',
    duration: isError ? 0 : undefined,
  });
}

const removeFlashListener = router.on('flash', (event) => {
  const flashToast = event.detail.flash.toast;
  if (flashToast) {
    showToast(flashToast);
  }
});

onBeforeUnmount(removeFlashListener);
</script>

<template>
  <span hidden />
</template>
