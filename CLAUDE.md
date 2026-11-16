# EwigeListe

Laravel-13-App zum Offline- und Online-Doppelkopf (Runden, Listen/Gruppen, Statistiken, Live-Spiel per Pusher). Läuft lokal über Laravel Herd unter `https://ewigeliste.test`.

Arbeitsweise, Texte und Umgebung (Hooks, Staging-Deploy, `db-sync.sh`, Cloud-Sessions) stehen in `.claude/rules/`. Diese Regeln, die Skills ohne Laravel-Bezug und mehrere Skripte kommen aus dem Home Repo, siehe `.claude/rules/home.md`.

@AGENTS.md

## Stack

- Inertia-SPA: Frontend unter `resources/js/inertia/` (Alias `@`, Pages in `Pages/`), Vue 3 + TS, Nuxt UI, Lucide-Icons (`i-lucide-*`), Inertia v3. Routen per Wayfinder (`@/routes/...`, `@/actions/...`), vom Vite-Plugin generiert und nicht eingecheckt.
- Jede GET-Route rendert per `Inertia::render()`; Blade gibt es nur für E-Mails, das Root-Template und die statische 503-Seite. Props über `app/Http/Resources/*` (`$wrap = null`), Controller schlank.
- Seiten, Komponenten, Resources und Tests: Skill `inertia-pages`.
- Schlanke Laravel-Struktur: Middleware, Routing und Exceptions in `bootstrap/app.php`, Scheduler in `routes/console.php`, Models in `app/Models`.
- Live-Spiel-Engine: `app/Models/LiveGame.php`, `app/Live/*`, Events in `app/Events/`.
- Begriffe: `GLOSSARY.md`; dort und im Code bleibt „Spieler“ der Fachbegriff. Entscheidungen: `docs/adr/`, die Gründe hinter dem heutigen Aufbau aus der Migration von Blade: `docs/migration.md`. Queue, Scheduler, Deploy und die Unterschiede zwischen Prod und lokal: `docs/betrieb.md`.

## Prüfungen

Vor jedem PR, alle grün (die CI prüft dasselbe):

```bash
vendor/bin/pint --test && php artisan test --compact
npm run build && npm run test:js && npm run type-check && npm run lint && npm run format
```

Lokal läuft die Queue über `database`: `php artisan queue:listen --tries=1` im eigenen Terminal-Tab, sonst werden Statistiken erst berechnet, sobald der Worker läuft. Bei geteilter Datenbank nur einen Worker für alle Workspaces.

## Branch und Deploy

Hauptbranch ist `master`. Jeder Push darauf startet „Deploy Production“. Das Repo ist öffentlich: Hosts, Pfade und Zugangsdaten nie committen.

Nach `./db-sync.sh` fehlen lokal die Avatare, sie kommen mit dem Dump nicht mit.
