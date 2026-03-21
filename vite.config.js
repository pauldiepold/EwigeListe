import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "@tailwindcss/vite";
import { fileURLToPath, URL } from 'node:url';

const herdDevHost = 'ewigeliste.test';

export default defineConfig(async () => {
    const { default: ui } = await import('@nuxt/ui/vite');

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/inertia/app.ts'],
                refresh: true,
                detectTls: herdDevHost,
            }),
            vue({
                template: {
                    compilerOptions: {
                        whitespace: 'preserve',
                    },
                },
            }),
            tailwindcss(),
            ui({
                router: 'inertia',
                ui: {
                    colors: {
                        primary: 'orange',
                        secondary: 'blue',
                        neutral: 'slate',
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': fileURLToPath(new URL('./resources/js/inertia', import.meta.url)),
            },
        },
    };
});
