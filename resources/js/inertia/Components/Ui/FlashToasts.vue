<script setup lang="ts">
import { onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import type { FlashToast } from '@/types/flash';

/**
 * Zeigt Inertia-Flash `toast` zentral an. Einmal an der App-Wurzel gemountet, damit der
 * Listener Seitenwechsel überlebt und auch die Meldung des ersten Seitenaufrufs erwischt.
 */
const toast = useToast();

const toastStyles: Record<
  FlashToast['type'],
  { color: 'success' | 'error'; icon: string; duration?: number }
> = {
  success: { color: 'success', icon: 'i-lucide-check-circle' },
  // Fehler bleiben stehen, bis man sie schließt.
  error: { color: 'error', icon: 'i-lucide-circle-alert', duration: 0 },
};

function showToast(flashToast: FlashToast): void {
  toast.add({ title: flashToast.message, ...toastStyles[flashToast.type] });
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
