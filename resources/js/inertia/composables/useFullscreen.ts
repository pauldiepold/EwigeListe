import { onMounted, onUnmounted, ref, type Ref } from 'vue';

/**
 * Vollbild für die ganze Seite, wo der Browser es kennt (auf dem iPhone nicht). Vollbild ist
 * ein Angebot, kein Zwang: Die Seite funktioniert ohne.
 */
export function useFullscreen(): {
  isSupported: Ref<boolean>;
  isFullscreen: Ref<boolean>;
  toggle: () => Promise<void>;
} {
  const isSupported = ref(false);
  const isFullscreen = ref(false);

  function sync(): void {
    isSupported.value = document.fullscreenEnabled === true;
    isFullscreen.value = document.fullscreenElement != null;
  }

  async function toggle(): Promise<void> {
    if (!isSupported.value) {
      return;
    }

    try {
      if (document.fullscreenElement) {
        await document.exitFullscreen();
      } else {
        await document.documentElement.requestFullscreen();
      }
    } catch {
      // Vom Browser abgelehnt: Die Seite bleibt, wie sie ist.
    }
  }

  onMounted(() => {
    sync();
    document.addEventListener('fullscreenchange', sync);
  });

  onUnmounted(() => {
    document.removeEventListener('fullscreenchange', sync);
  });

  return { isSupported, isFullscreen, toggle };
}
