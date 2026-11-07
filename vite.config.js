import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "@tailwindcss/vite";
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
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
            wayfinder({
                path: 'resources/js/inertia',
                formVariants: true,
            }),
            ui({
                router: 'inertia',
                ui: {
                    colors: {
                        primary: 'orange',
                        secondary: 'blue',
                        neutral: 'slate',
                    },
                    // Toasts heben sich sonst kaum vom Seitenhintergrund ab.
                    toast: {
                        slots: {
                            root: 'bg-elevated ring-accented shadow-xl',
                        },
                    },
                    // Am Handy trägt nur der aktive Tab Text, sonst kürzt Nuxt UI alle Beschriftungen.
                    // Nur bei Tabs mit Icon; `sr-only` lässt den Namen für Screenreader stehen.
                    tabs: {
                        slots: {
                            trigger: 'max-sm:data-[state=active]:shrink-0',
                            label: 'max-sm:group-data-[state=inactive]:group-has-[[data-slot=leadingIcon]]:sr-only',
                        },
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
