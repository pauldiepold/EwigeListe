import { onMounted, onUnmounted } from 'vue';

/**
 * Ein gesperrtes Handy verpasst Pushes. Sobald die Seite wieder sichtbar wird, ruft das
 * `refresh` auf, das die Seite neu lädt.
 */
export function useRefreshOnVisible(refresh: () => void): void {
  function refreshWhenVisible(): void {
    if (document.visibilityState === 'visible') {
      refresh();
    }
  }

  onMounted(() => document.addEventListener('visibilitychange', refreshWhenVisible));
  onUnmounted(() => document.removeEventListener('visibilitychange', refreshWhenVisible));
}
