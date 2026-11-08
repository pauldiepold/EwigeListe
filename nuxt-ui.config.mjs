/**
 * Optionen des Nuxt-UI-Vite-Plugins. Eigene Datei, weil `vite.config.mjs` und `vitest.config.mts`
 * sie teilen: Tests rendern die Komponenten so wie die App.
 */
export default {
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
};
