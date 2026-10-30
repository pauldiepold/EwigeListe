import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ui from '@nuxt/ui/vue-plugin';

const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');

createInertiaApp({
  title: (title) => (title ? `${title} - Ewige Liste` : 'Ewige Liste'),
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, pages),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ui)
      .mount(el);
  },
});

const isLocalHost = location.hostname === 'localhost' || location.hostname.endsWith('.test');

if ('serviceWorker' in navigator && !isLocalHost) {
  void navigator.serviceWorker.register('/service-worker.js');
}
