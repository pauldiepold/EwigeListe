# Migration: Blade → Inertia-SPA

Leitdokument für die Migration: Ziel, Leitplanken, Roadmap, Fortschritt und Entscheidungen. Die Details zu jedem Arbeitspaket stehen im verlinkten GitHub-Issue. Begriffe: `GLOSSARY.md`.

**Stand:** Phase 0 erledigt, Phase 1 offen · Branch `inertia-migration` · Go-Live-PR #16

## Ziel

Die gesamte App läuft als Laravel + Inertia + Vue-3-SPA (TypeScript, Nuxt UI). Es gibt kein Blade-Frontend und keine Legacy-Vue-Komponenten mehr, und das Design ist modernisiert. Danach folgen Backend-Modernisierung und neue Features.

## Leitplanken

- **Big Bang:** Alles wird auf `inertia-migration` fertig migriert und auf Staging mit dem Freundeskreis getestet. Erst der Merge nach `master` ist der Go-Live. Auf Prod gibt es nie einen gemischten Stand.
- **Zwischendurch darf etwas kaputt sein.** Keine Brücken zwischen Inertia und Blade bauen; ein Link auf eine noch nicht migrierte Seite darf scheitern.
- **Legacy wird pro Seite gelöscht:** im selben PR, der die Seite migriert. Bis dahin ist die Blade-Datei die Spezifikation und muss vor der Migration gelesen werden. Bereits toter Code wird sofort entfernt.
- **Pro GET-Route genau ein Render-Pfad:** `Inertia::render()`. Blade bleibt nur für E-Mails.
- **TDD:** Jede migrierte Route bekommt Feature-Tests (`assertInertia`), jede angefasste Mutation Tests für Redirect und Validierung. Frontend: Type-Check und Lint; manuelle Prüfung auf Staging.
- **Backend nur so weit anfassen, wie die Seite es braucht.** Übrige Modernisierung: #38.
- **Funktional vor schön:** Pro Seite nah am bisherigen Look und sauber strukturiert. Das Design kommt gebündelt in Phase 4.
- **Feature-Freeze:** Feature-Issues tragen das Label `nach-migration`. Kleine Features, die genau die gerade migrierte Seite betreffen, dürfen mit.

## Arbeitsweise

1. Issue wählen (Reihenfolge siehe Roadmap), Branch `mig/<nr>-kurzname` von `inertia-migration`.
2. TDD: zuerst ein Test, der fehlschlägt, dann die Umsetzung.
3. Vor dem PR: `php artisan test --compact`, `npm run type-check`, `npm run lint`, `npm run format:fix`.
4. PR nach `inertia-migration` mit `Closes #nr`, `/code-review`, Paul merged per Squash.
5. **Im selben PR** wird der Status in diesem Dokument aktualisiert (Roadmap und Routen-Matrix).
6. Staging-Deploy bei Bedarf über den Workflow „Deploy Staging“ auf `inertia-migration` (optional mit Prod-Dump).

## Roadmap

| Phase | Inhalt | Issues | Status |
|---|---|---|---|
| 0 | Repo aufräumen, Cursor → Claude Code, PRs/Branches aufräumen | #26 | ✅ |
| 1 | Testsuite reparieren, Inertia-Testmuster, CI; Tooling-Entscheidungen (Boost-Update, Inertia v3, Wayfinder, Hooks, Larastan) | #27, #48 | todo |
| 2 | Review & Korrektur des Cursor-Stands, Tests für migrierte Seiten | #28 | todo |
| 3 | Restliche Seiten ohne Live-Spiel | #29, #30, #31, #32, #33 | todo |
| 4 | Design: bis zu 4 Spikes, Abstimmung, Rollout | #34, #35 | todo |
| 5 | Live-Spiel im neuen Design | #36 | todo |
| 6 | Legacy-Abbau, Spieleabend auf Staging, Go-Live | #37 | todo |
| danach | Backend-Modernisierung, Feature-Issues | #38, Label `nach-migration` | — |

#48 ist vor #28 abgeschlossen, damit die bestehenden Seiten nur einmal auf den finalen Stack gebracht werden.

Reihenfolge in Phase 3: #29 Benutzer → #30 Liste → #31 Profil → #32 Runde → #33 Rest. Die Listen-Seite kommt vor dem Profil, weil dort die wiederverwendbaren Komponenten entstehen (Badges, Charts, Statistik-Tabellen, Archiv).

## Go-Live-Kriterien

1. Jede Route ist migriert oder bewusst gestrichen; Blade nur noch für E-Mails.
2. Tests, Type-Check und Lint sind grün.
3. Legacy-JS, Legacy-CSS und Legacy-Abhängigkeiten sind entfernt.
4. Mindestens ein echter Spieleabend auf Staging inklusive Live-Spiel ohne funktionale Fehler.
5. Rollback-Plan: `master` vor dem Merge taggen, DB-Backup, Datenmigrationen per wiederholbarem Neuberechnungs-Befehl.

## Routen-Matrix

Status: `todo` · `in_progress` · `✅` · `gestrichen`. Bei ✅-Zeilen aus der Cursor-Phase steht der Review noch aus (#28).

| Methode | URI | Route-Name | Action | Ziel-Page | Issue | Status | Hinweise |
|---|---|---|---|---|---|---|---|
| GET | `/`, `home` | `index`, `home` | `HomeController@index` | `Home/Index` | #28 | ✅ | Referenzimplementierung |
| GET | `runde/erstellen` | `rounds.create` | `RoundController@create` | `Rounds/Create` | #28 | ✅ | Nuxt-UI-Spike |
| GET | `rundenarchiv/{group?}` | `rounds.index` | `RoundController@index` | `Rounds/Index` | #28 | ✅ | Sortierung serverseitig (`sort`, `direction`), `UTable` mit `manualSorting` |
| GET | `listen` | `groups.index` | `GroupController@index` | `Groups/Index` | #28 | ✅ | |
| GET | `liste/erstellen` | `groups.create` | `GroupController@create` | `Groups/Create` | #28 | ✅ | nur Namensfeld |
| GET | `login`, `register`, `password/*`, `register/quick`, `login/social/{socialiteUser}` | diverse | Auth-Controller | `Auth/*` | #28 | ✅ | `UAuthForm`, reCAPTCHA, Socialite-Attach |
| GET | `datenschutz`, `impressum`, `regeln` | gleichnamig | `StaticPageController` | `Static/*` | #28 | ✅ | |
| GET | `users/{user}/edit` | `users.edit` | `UserController@edit` | `Users/Edit` | #29 | todo | 4 PATCH-Formulare, Avatar-Upload mit Cropper |
| GET | `liste`, `liste/{group}` | `ewigeListe`, `groups.show` | `GroupController@show` | `Groups/Show` | #30 | todo | mit `records`/`stats` (#39) und POST für beitreten/verlassen/schließen (#40) |
| GET | `profil/{player}/{group?}` | — → `players.show` | `PlayerController@show` | `Players/Show` | #31 | todo | Route benennen |
| GET | `runde/{round}` | `rounds.show` | `RoundController@show` | `Rounds/Show` | #32 / #36 | todo | erst offline (#32), Live-Tab in Phase 5 (#36) |
| GET | `rounds/current` | `rounds.current` | `RoundController@current` | Redirect | #32 | todo | |
| GET | `report` | — | `ReportController@report` | — | #33 | todo → gestrichen | wird durch einen Artisan-Befehl zum Neuberechnen ersetzt |
| GET | `listen/calculate`, `liste/calculate*`, `players/calculate*` | — | Group-/PlayerController | — | #33 | todo → gestrichen | dito |
| — | Fehlerseiten 401–503 | — | Exception-Handler | `Error` | #33 | todo | |
| — | Kommentare | — | `CommentController` | ? | #33 | todo | nur noch in der alten Home-Blade eingebunden; Nutzung prüfen, sonst streichen |
| GET | `test`, `testClient`, `mail` | — | `TestController` | — | #26 | gestrichen | Routen, Controller und Views entfernt |
| — | E-Mail-Verifizierung | — | `VerificationController` | — | #26 | gestrichen | Controller und `auth/verify.blade.php` entfernt |

**Keine Vollseiten, bleiben als Endpunkte:** `charts/*` (JSON, per `fetch`), `api/rounds/{round}/fetchData`, `api/rounds/{round}/game`, `api/games/{game}`, `api/live/*`, `api/users/{user}/avatar`, `rounds/ajax/*` (DataTables, entfällt mit #45), Formular-POSTs/PATCHes/DELETEs, OAuth (`auth/redirect/*`, `callback/*`), Framework-Routen (`telescope`, `_debugbar`, `broadcasting/auth`).

## Entscheidungen

| Datum | Entscheidung | Begründung |
|---|---|---|
| 2026-03 | Nuxt UI als UI-Framework für alle Inertia-Seiten | Spike an `rounds.create`: hohe Entwicklungsgeschwindigkeit für Formulare, Tabellen und Overlays; über das Theme anpassbar |
| 2026-03 | ESLint + Prettier + vue-tsc (ohne oxlint/oxfmt) | Pilotphase, etablierte Tools |
| 2026-03 | Ziggy für benannte Routen im Frontend (`useAppRoute()`) | keine hart codierten URLs |
| 2026-03 | `fetch` statt axios für einfache GETs | weniger Abhängigkeiten; axios nur bei klarem Mehrwert |
| 2026-03 | Lucide-Icons (`i-lucide-*`) statt FontAwesome | in Nuxt UI integriert |
| 2026-10-28 | Big Bang statt Parallelbetrieb | kein Zeitdruck; gemischter Stand auf Prod unerwünscht; Test auf Staging mit dem Freundeskreis |
| 2026-10-28 | Legacy pro Seite löschen, nicht vorab | Blade/Legacy-Vue sind bis zur Migration die Spezifikation |
| 2026-10-28 | Tests sind Pflicht, Arbeit per TDD | Sicherheitsnetz für umfangreiche, teils autonome Umbauten |
| 2026-10-28 | Live-Spiel bleibt im Umfang, kommt aber nach Design | Live-UI wird direkt im neuen Design gebaut statt zweimal |
| 2026-10-28 | Design-Spikes bleiben innerhalb von Nuxt UI | kein Framework-Wechsel; Varianten unterscheiden sich in Theme und Layout |
| 2026-10-28 | Claude Code als einziges Agent-Tooling; `.cursor/` wird aufgelöst | eine Quelle für Regeln und Skills |
| 2026-10-28 | Test-/Report-Routen streichen, Neuberechnung als Artisan-Befehl | Testseiten ungenutzt; ein Befehl wird ohnehin für den Rollback gebraucht |
| 2026-10-28 | „Profil“ bleibt als Begriff (Spieler in einer Liste) | passt zur Seite für die Benutzer; Umbenennung im Code zu teuer |

## Offene Fragen

- **Pusher vs. Laravel Reverb** für das Live-Spiel; zusammen mit `@laravel/echo-vue`. Entscheidung zu Beginn von Phase 5 (#36), ADR-Kandidat.
- **Echtzeit-Aktualisierung der Offline-Runde** (`RoundUpdated`): schon in #32 oder erst in Phase 5?
- **Kommentare:** werden sie in Prod noch genutzt? (#33)

## Learnings

- Die Routen-Matrix kann von der Realität abweichen: Vor der Umsetzung immer die echte Blade lesen und prüfen, welche Variablen das Template wirklich nutzt. Ungenutzte Controller-Daten werden entfernt, nicht als Prop weitergereicht (Beispiel `groups.create`).
- Mit `UTable` und `paginate()` liegt nur die aktuelle Seite im Browser: Sortieren und Filtern gehört ins Backend (Sortierspalten auf eine Whitelist, stabiler Tie-Breaker), im Frontend `manualSorting`.
- Positionsbasiertes JSON mit HTML-Links (`Group::records`/`stats`) erzwingt `v-html` und Übersetzungsschritte in den Resources, siehe `docs/tech-debt/group-positional-json.md` und #39.
- Inertia-Links auf Seiten, die noch Blade sind, liefern eine HTML-Antwort statt einer Inertia-Antwort. Wegen des Big Bangs nehmen wir das bewusst in Kauf.
- Legacy-Vue-Komponenten werden in `resources/js/app.js` per Glob global registriert. Ob eine Komponente noch genutzt wird, zeigt nur ein Grep nach ihrem Tag (PascalCase und kebab-case) in Blade und anderen Legacy-Komponenten, kein Import.
- Boost-Guidelines und -Skills nach Paket-Updates mit `php artisan boost:update` neu erzeugen, nicht von Hand pflegen. Die Guidelines landen über `config/boost.php` in `AGENTS.md` (von `CLAUDE.md` importiert), die Skills in `.claude/skills/`.
