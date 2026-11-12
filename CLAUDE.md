# EwigeListe

Laravel-13-App zum Offline- und Online-Doppelkopf (Runden, Listen/Gruppen, Statistiken, Live-Spiel per Pusher). Läuft lokal über Laravel Herd unter `https://ewigeliste.test`.

@AGENTS.md

## Stack

- Inertia-SPA: Frontend unter `resources/js/inertia/` (Alias `@`, Pages in `Pages/`), Vue 3 + TS, Nuxt UI, Lucide-Icons (`i-lucide-*`), Inertia v3. Routen per Wayfinder (`@/routes/...`, `@/actions/...`), vom Vite-Plugin generiert und nicht eingecheckt.
- Jede GET-Route rendert per `Inertia::render()`; Blade gibt es nur für E-Mails, das Root-Template und die statische 503-Seite. Props über `app/Http/Resources/*` (`$wrap = null`), Controller schlank.
- Seiten, Komponenten, Resources und Tests: Skill `inertia-pages`.
- Schlanke Laravel-Struktur: Middleware, Routing und Exceptions in `bootstrap/app.php`, Scheduler in `routes/console.php`, Models in `app/Models`.
- Live-Spiel-Engine: `app/Models/LiveGame.php`, `app/Live/*`, Events in `app/Events/`.
- Sichtbare Texte (UI, Meldungen, aria-labels) in der Du-Form und geschlechtsneutral: „Person“ oder „wer …“ statt „Spieler“, „Gewinner“ oder „Administrator“. Wo das holprig wird, generisches Femininum („Zuschauerinnen“), keine Partizipien wie „Zuschauende“. Im Code und in `GLOSSARY.md` bleibt „Spieler“ der Fachbegriff.
- Begriffe: `GLOSSARY.md`. Entscheidungen: `docs/adr/`, die Gründe hinter dem heutigen Aufbau aus der Migration von Blade: `docs/migration.md`. Queue, Scheduler und Deploy auf Prod und Staging: `docs/betrieb.md`.

## Prüfungen

Vor jedem PR, alle grün (die CI prüft dasselbe):

```bash
vendor/bin/pint --test && php artisan test --compact
npm run build && npm run test:js && npm run type-check && npm run lint && npm run format
```

Prettier, `php -l` und Pint laufen zusätzlich per Hook nach jedem Edit (`.claude/hooks/post-edit.mjs`).

Lokal läuft die Queue über `database`: `php artisan queue:listen --tries=1` im eigenen Terminal-Tab, sonst werden Statistiken erst berechnet, sobald der Worker läuft. Bei geteilter Datenbank nur einen Worker für alle Workspaces.

## Lokale Datenbank

`./db-sync.sh` (Git Bash) holt den neuesten Prod-Dump per FTP (Zugang über `FTP_*` in `.env`) und spielt ihn in die lokale Datenbank aus `.env`, nur bei `APP_ENV=local`. Ohne `mysql` im `PATH` importiert es über den Docker-Container `phpstormprojects-mysql-1` (`MYSQL_CONTAINER` in `.env` überschreibt den Namen). Danach `php artisan migrate`, falls der Branch neuere Migrationen hat. Avatare kommen nicht mit, lokal fehlen die Bilder.

## Arbeitsweise

- Jede Änderung, auch reine Doku, ohne Rückfrage auf einem eigenen Branch von `master` und per PR (Skill `mattpocock-skills:pr`). Jeder Push auf `master` startet „Deploy Production“, deshalb landet dort nur der gemergte PR.
- Tickets immer per Skill `mattpocock-skills:implement` umsetzen, auch direkt nach einem Grilling; ohne den Skill nur, wenn Paul es ausdrücklich sagt. Abschluss: alle Prüfungen grün, PR nach `master`, erst nach Pauls Abnahme mergen (Squash) und das Issue schließen.
- „Deploy“ heißt Staging; Produktion nur, wenn Paul sie ausdrücklich nennt. Staging deployt nur manuell: ohne Rückfrage, wenn `/implement` ein größeres Feature mit offenem PR abschließt (in der Cloud-Session nach grüner CI), sonst auf Pauls Wunsch. Kleine Änderungen nimmt Paul lokal ab.
  ```bash
  gh api repos/pauldiepold/EwigeListe/actions/workflows/deploy-staging.yml/dispatches -f ref=<branch> -f inputs[sync_db]=yes
  ```
  Den Lauf bis zum Ende beobachten und Paul mit „deployed“ melden.
- Kleine Aufräumarbeiten im Umfeld des Tickets (Review-Funde, Inkonsistenzen in angefassten Dateien) gleich mit erledigen; nur Größeres als eigenes Issue abgrenzen.
- Kleine Anfragen klein lösen. Übersteigt der „richtige“ Weg den Nutzen deutlich, die einfachste Variante wählen und in wenigen Sätzen begründen.
- Fehlt in der Shell ein Tool (`php`, `composer`, Netzwerk), Paul um die Ausführung bitten.
- Dauerhaftes Feedback zum Vorgehen in `CLAUDE.md` bzw. im passenden Skill festhalten.
