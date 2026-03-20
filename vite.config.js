import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

const herdDevHost = 'ewigeliste.test';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
    ],
});
