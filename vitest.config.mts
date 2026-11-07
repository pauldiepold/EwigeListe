import { fileURLToPath, URL } from 'node:url';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vitest/config';
import nuxtUiOptions from './nuxt-ui.config.mjs';

/**
 * Eigene Config statt `mergeConfig` mit `vite.config.js`: `laravel()` bricht unter `CI` im
 * Serve-Modus ab, und `wayfinder()` riefe bei jedem Lauf `php artisan` auf. Die Wayfinder-Dateien
 * erzeugt vorher `npm run build`. `ui()` bleibt drin, weil es `U*`-Komponenten und `useToast`
 * automatisch importiert.
 */
export default defineConfig(async () => {
  const { default: ui } = await import('@nuxt/ui/vite');

  return {
    plugins: [
      vue({ template: { compilerOptions: { whitespace: 'preserve' } } }),
      ui(nuxtUiOptions),
    ],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./resources/js/inertia', import.meta.url)),
      },
    },
    test: {
      environment: 'jsdom',
      include: ['resources/js/inertia/**/__tests__/*.test.ts'],
      setupFiles: ['resources/js/inertia/testing/setup.ts'],
      restoreMocks: true,
    },
  };
});
