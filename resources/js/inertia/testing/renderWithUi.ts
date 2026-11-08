import { defineComponent, h, type Component } from 'vue';
import { render } from '@testing-library/vue';
import ui from '@nuxt/ui/vue-plugin';
import UApp from '@nuxt/ui/components/App.vue';

/**
 * Rendert eine Komponente wie in der App: mit Nuxt-UI-Plugin und innerhalb von `UApp`, das
 * Toasts und Overlays bereitstellt. Abfragen laufen über `screen`, weil Modals und Toasts in
 * `document.body` teleportieren. `rerender(props)` reicht neue Props an die Komponente durch.
 */
export function renderWithUi(component: Component, props: Record<string, unknown>) {
  const AppWrapper = defineComponent({
    inheritAttrs: false,
    setup(_, { attrs }) {
      return () => h(UApp, null, { default: () => h(component, attrs) });
    },
  });

  return render(AppWrapper, { props, global: { plugins: [ui] } });
}
