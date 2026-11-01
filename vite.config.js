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
                    // PROTOTYP #34: Die Varianten hängen als `design-*` an den Komponenten.
                    button: {
                        slots: {
                            base: 'design-b:uppercase design-b:tracking-wide design-c:rounded-full design-c:font-semibold design-d:font-bold',
                        },
                        compoundVariants: [
                            {
                                color: 'primary',
                                variant: 'solid',
                                class: 'design-b:bg-transparent design-b:text-primary design-b:ring design-b:ring-inset design-b:ring-primary design-b:hover:bg-primary/10 design-c:px-4 design-c:py-2.5',
                            },
                            {
                                color: 'primary',
                                variant: 'outline',
                                class: 'design-d:ring-0 design-d:bg-primary/15',
                            },
                            {
                                color: 'neutral',
                                variant: 'outline',
                                class: 'design-d:ring-0 design-d:bg-accented',
                            },
                        ],
                    },
                    tabs: {
                        slots: {
                            list: 'design-c:rounded-full',
                            indicator: 'design-c:rounded-full',
                            trigger: 'max-sm:data-[state=active]:shrink-0 design-b:uppercase design-b:tracking-wider design-b:text-xs design-c:rounded-full design-d:font-bold',
                            // Am Handy trägt nur der aktive Tab Text, sonst schneidet Nuxt UI alle ab.
                            label: 'max-sm:group-data-[state=inactive]:hidden',
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
