import { onMounted, onUnmounted, ref, toValue, watch, type MaybeRefOrGetter, type Ref } from 'vue';
import { router } from '@inertiajs/vue3';

/**
 * Hält den aktiven Tab im URL-Hash, damit er Reload und Links überlebt. Der Zurück-Button
 * verlässt die Seite, statt durch die Tabs zu springen.
 */
export function useTabInUrlHash(
  tabs: MaybeRefOrGetter<Array<{ value: string }>>,
  defaultTab: string,
  pageComponent: string,
): Ref<string> {
  const activeTab = ref(defaultTab);

  /** `replaceState` behält den History-State von Inertia; ein neuer Eintrag entsteht nicht. */
  function writeActiveTabToUrl(): void {
    window.history.replaceState(window.history.state, '', `#${activeTab.value}`);
  }

  watch(activeTab, writeActiveTabToUrl);

  /** Inertia übernimmt den Hash nach einer Antwort nur bei exakt gleicher Query-Reihenfolge. */
  const stopRestoringHash = router.on('success', (event) => {
    if (event.detail.page.component === pageComponent) {
      writeActiveTabToUrl();
    }
  });

  onMounted(() => {
    const tabFromHash = window.location.hash.slice(1);
    if (toValue(tabs).some((tab) => tab.value === tabFromHash)) {
      activeTab.value = tabFromHash;
    }
  });

  onUnmounted(stopRestoringHash);

  return activeTab;
}
