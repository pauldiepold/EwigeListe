# EwigeListe

Laravel-13-App zum Offline- und Online-Doppelkopf (Runden, Listen/Gruppen, Statistiken, Live-Spiel per Pusher). Läuft lokal über Laravel Herd unter `https://ewigeliste.test`.

@AGENTS.md

## Frontend: Inertia-SPA (Migration von Blade abgeschlossen)

- Die Migration ist seit dem 2026-11-08 live (#16). `docs/migration.md` bleibt das Protokoll ihrer Entscheidungen und Learnings; neue Arbeit geht von `master` ab.
- Seiten migrieren per Skill `blade-to-inertia-migration` (TDD, Legacy im selben PR löschen).
- Neuer Code **nur** unter `resources/js/inertia/` (Alias `@`, Pages in `resources/js/inertia/Pages/`): Vue 3 + TS, Nuxt UI, Lucide-Icons (`i-lucide-*`), Inertia v3, Routen per Wayfinder (`@/routes/...`, `@/actions/...`; vom Vite-Plugin generiert, nicht eingecheckt).
- Das Legacy-Frontend ist entfernt (#37): Vite hat nur noch einen JS-Entry (`resources/js/inertia/app.ts`), Lint, Format und Type-Check decken ganz `resources/js` ab.
- Ziel pro GET-Route: `Inertia::render()`. Blade bleibt nur für E-Mails, das Inertia-Root-Template und die statische 503-Seite.
- Props über `app/Http/Resources/*` (`$wrap = null`), Controller schlank.
- Domänen-Glossar: `GLOSSARY.md`, Entscheidungen: `docs/adr/`.

## Befehle

- `php artisan test --compact` (Tests), `npm run build`, `vendor/bin/pint --test` (PHP-Formatierung)
- Nach Frontend-Änderungen: `npm run test:js && npm run type-check && npm run lint` (Prettier, `php -l` und Pint laufen per Hook nach jedem Edit, `.claude/hooks/post-edit.mjs`)
- Komponententests einzeln: `npm run test:js -- EnterGameModal`. Wann und wie: Skill `blade-to-inertia-migration`, Abschnitt „Komponententests“
- `php artisan queue:listen --tries=1` im eigenen Terminal-Tab: Lokal läuft die Queue über `database`. Ohne Worker werden Statistiken erst berechnet, sobald er wieder läuft. Bei geteilter Datenbank nur einen Worker für alle Workspaces starten. Prod und Staging: Cron-Skript statt Worker, siehe `docs/betrieb.md`.

## Struktur-Hinweise

- Schlanke Laravel-11+-Struktur: Middleware, Routing, Scheduler und Exceptions in `bootstrap/app.php`, Provider in `bootstrap/providers.php`. Models in `app/Models` (z. B. `App\Models\Round`), Controller-Routen als `[Controller::class, 'method']`.
- Live-Spiel-Engine: `app/Models/LiveGame.php`, `app/Live/*`, Events in `app/Events/`.

## Arbeitsweise

- Kleine Aufräumarbeiten im Umfeld des Tickets (Review-Funde, Inkonsistenzen in angefassten Dateien) gleich mit erledigen, statt sie als „offen“ zu melden. Nur Größeres oder Seiten eines anderen Tickets abgrenzen.
- Kleine Anfragen klein lösen. Übersteigt der „richtige“ Weg den Nutzen deutlich, die einfachste Variante wählen und in wenigen Sätzen begründen.
- Fehlt in der Shell ein Tool (`php`, `composer`, Netzwerk), Paul um die Ausführung bitten statt einen Workaround zu bauen.
- Dauerhaftes Feedback zum Vorgehen in `CLAUDE.md` bzw. im passenden Skill festhalten.
- Jede Änderung, auch reine Doku, ohne Rückfrage auf einem eigenen Branch und per PR (Skill `mattpocock-skills:pr`). Nie direkt auf `master` committen: Jeder Push dort startet „Deploy Production“.
- Tickets immer per Skill `mattpocock-skills:implement` umsetzen, auch direkt nach einem Grilling. Ohne den Skill nur, wenn Paul es ausdrücklich sagt.
- „Deploy“ heißt immer Staging (Workflow „Deploy Staging“, siehe unten). Produktion nur, wenn Paul sie ausdrücklich nennt.
- Ticket-Abschluss (z. B. am Ende von `/implement`): Alle Prüfungen ausführen (`docs/migration.md`, Arbeitsweise, Schritt 3), PR nach `master`. Erst nach Pauls Abnahme mergen (Squash) und das Issue schließen. Der Merge deployt Prod.
- Staging deployt nur noch manuell (Workflow „Deploy Staging“, kein Push-Trigger). Ohne Rückfrage starten, wenn `/implement` ein größeres Feature mit offenem PR abschließt (in der Cloud-Session nach grüner CI), sonst nur auf Pauls Wunsch: `gh api repos/pauldiepold/EwigeListe/actions/workflows/deploy-staging.yml/dispatches -f ref=<branch> -f inputs[sync_db]=yes`, bis zum Ende beobachten und Paul mit „deployed“ melden. Kleine Änderungen nimmt Paul lokal ab.
