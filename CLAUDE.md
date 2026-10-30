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

- `php artisan test --compact` (Tests), `npm run build`, `vendor/bin/pint --test` (PHP-Formatierung)
- Nach Frontend-Änderungen: `npm run type-check && npm run lint` (Prettier, `php -l` und Pint laufen per Hook nach jedem Edit, `.claude/hooks/post-edit.mjs`)

## Struktur-Hinweise

- Laravel-10-Struktur (Kernel, RouteServiceProvider); Models direkt in `app/` (z. B. `App\Round`), Controller-Routen als `'Controller@method'`-Strings.
- Live-Spiel-Engine: `app/LiveGame.php`, `app/Live/*`, Events in `app/Events/`.

## Arbeitsweise

- Kleine Aufräumarbeiten im Umfeld des Tickets (Review-Funde, Inkonsistenzen in angefassten Dateien) gleich mit erledigen, statt sie als „offen“ zu melden. Nur Größeres oder Seiten eines anderen Tickets abgrenzen.
- Kleine Anfragen klein lösen. Übersteigt der „richtige“ Weg den Nutzen deutlich, die einfachste Variante wählen und in wenigen Sätzen begründen.
- Fehlt in der Shell ein Tool (`php`, `composer`, Netzwerk), Paul um die Ausführung bitten statt einen Workaround zu bauen.
- Dauerhaftes Feedback zum Vorgehen in `CLAUDE.md` bzw. im passenden Skill festhalten.
- Ticket-Abschluss (z. B. am Ende von `/implement`) hängt von der Session ab. Erst nach Pauls Abnahme mergen (Squash) und das Issue schließen. Details: `docs/migration.md`, Arbeitsweise.
  - **Lokale Session:** Alle Prüfungen lokal ausführen (`docs/migration.md`, Arbeitsweise, Schritt 3) und ohne Warten auf die GitHub-CI weitermachen. Paul nimmt lokal ab, der Staging-Deploy entfällt.
  - **Cloud-Session:** Wenn die CI grün ist, Workflow „Deploy Staging“ auf dem Feature-Branch mit `sync_db=yes` starten (`gh api repos/pauldiepold/EwigeListe/actions/workflows/deploy-staging.yml/dispatches -f ref=<branch> -f inputs[sync_db]=yes`), bis zum Ende beobachten und Paul mit „deployed“ melden.
