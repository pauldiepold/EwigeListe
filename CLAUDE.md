# EwigeListe

Laravel-12-App zum Offline- und Online-Doppelkopf (Runden, Listen/Gruppen, Statistiken, Live-Spiel per Pusher). Läuft lokal über Laravel Herd unter `https://ewigeliste.test`.

@AGENTS.md

## Laufende Migration: Blade → Inertia-SPA

- Leitdokument (Plan, Fortschritt, Entscheidungen): `docs/migration.md`. Big Bang auf Branch `inertia-migration`; Status im selben PR pflegen.
- Neuer Code **nur** unter `resources/js/inertia/` (Alias `@`): Vue 3 + TS, Nuxt UI, Lucide-Icons (`i-lucide-*`), Ziggy via `useAppRoute()`.
- Legacy: `resources/views/**` (Blade) + `resources/js/components/**` (alte Vue-Komponenten, Entry `resources/js/app.js`). Nicht erweitern.
- Pro GET-Route genau ein Render-Pfad: `view()` **oder** `Inertia::render()`.
- Props über `app/Http/Resources/*` (`$wrap = null`), Controller schlank.
- Domänen-Glossar: `GLOSSARY.md`, Entscheidungen: `docs/adr/`.

## Befehle

- `php artisan test --compact` (Tests), `npm run build`
- Nach Frontend-Änderungen: `npm run type-check && npm run lint && npm run format:fix`
- Kein Laravel Pint.

## Struktur-Hinweise

- Laravel-10-Struktur (Kernel, RouteServiceProvider); Models direkt in `app/` (z. B. `App\Round`), Controller-Routen als `'Controller@method'`-Strings.
- Live-Spiel-Engine: `app/LiveGame.php`, `app/Live/*`, Events in `app/Events/`.
