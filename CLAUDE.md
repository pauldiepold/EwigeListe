# EwigeListe

Laravel-13-App zum Offline- und Online-Doppelkopf (Runden, Listen/Gruppen, Statistiken, Live-Spiel per Pusher). Läuft lokal über Laravel Herd unter `https://ewigeliste.test`.

@AGENTS.md

## Laufende Migration: Blade → Inertia-SPA

- Leitdokument (Plan, Fortschritt, Entscheidungen): `docs/migration.md`. Big Bang auf Branch `inertia-migration`; Status im selben PR pflegen.
- Seiten migrieren per Skill `blade-to-inertia-migration` (TDD, Legacy im selben PR löschen).
- Neuer Code **nur** unter `resources/js/inertia/` (Alias `@`, Pages in `resources/js/inertia/Pages/`): Vue 3 + TS, Nuxt UI, Lucide-Icons (`i-lucide-*`), Inertia v3, Routen per Wayfinder (`@/routes/...`, `@/actions/...`; vom Vite-Plugin generiert, nicht eingecheckt).
- Legacy: `resources/views/**` (Blade) + `resources/js/components/**` (alte Vue-Komponenten, Entry `resources/js/app.js`). Nicht erweitern; bis zur Migration ist es die Spezifikation der Seite.
- Ziel pro GET-Route: `Inertia::render()`. Blade bleibt nur für E-Mails.
- Props über `app/Http/Resources/*` (`$wrap = null`), Controller schlank.
- Domänen-Glossar: `GLOSSARY.md`, Entscheidungen: `docs/adr/`.

## Befehle

- `php artisan test --compact` (Tests), `npm run build`
- Nach Frontend-Änderungen: `npm run type-check && npm run lint` (Prettier und `php -l` laufen per Hook nach jedem Edit, `.claude/hooks/post-edit.mjs`)
- Laravel Pint noch nicht ausführen: kommt nach #28 als einmaliger Formatierungs-Commit, danach im Hook.

## Struktur-Hinweise

- Laravel-10-Struktur (Kernel, RouteServiceProvider); Models direkt in `app/` (z. B. `App\Round`), Controller-Routen als `'Controller@method'`-Strings.
- Live-Spiel-Engine: `app/LiveGame.php`, `app/Live/*`, Events in `app/Events/`.

## Arbeitsweise

- Kleine Anfragen klein lösen. Übersteigt der „richtige“ Weg den Nutzen deutlich, die einfachste Variante wählen und in wenigen Sätzen begründen.
- Fehlt in der Shell ein Tool (`php`, `composer`, Netzwerk), Paul um die Ausführung bitten statt einen Workaround zu bauen.
- Dauerhaftes Feedback zum Vorgehen in `CLAUDE.md` bzw. im passenden Skill festhalten.
