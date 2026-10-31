# Migration: Blade → Inertia-SPA

Leitdokument für die Migration: Ziel, Leitplanken, Roadmap, Fortschritt und Entscheidungen. Die Details zu jedem Arbeitspaket stehen im verlinkten GitHub-Issue. Begriffe: `GLOSSARY.md`.

**Stand:** Phase 0 bis 3 erledigt, Phase 4 offen · Branch `inertia-migration` · Go-Live-PR #16

## Ziel

Die gesamte App läuft als Laravel + Inertia + Vue-3-SPA (TypeScript, Nuxt UI). Es gibt kein Blade-Frontend und keine Legacy-Vue-Komponenten mehr, und das Design ist modernisiert. Danach folgen Backend-Modernisierung und neue Features.

## Leitplanken

- **Big Bang:** Alles wird auf `inertia-migration` fertig migriert und auf Staging mit dem Freundeskreis getestet. Erst der Merge nach `master` ist der Go-Live. Auf Prod gibt es nie einen gemischten Stand.
- **Zwischendurch darf etwas kaputt sein.** Keine Brücken zwischen Inertia und Blade bauen; ein Link auf eine noch nicht migrierte Seite darf scheitern.
- **Links und Redirects gleich im Zielbild:** `<Link>` bzw. `UButton :to` mit Wayfinder, Redirects per `redirect()->route()`, auch wenn das Ziel noch Blade ist. Kein `<a>`, kein `Inertia::location()` als Übergang; das Ziel funktioniert, sobald seine Seite migriert ist.
- **Legacy wird pro Seite gelöscht:** im selben PR, der die Seite migriert. Bis dahin ist die Blade-Datei die Spezifikation und muss vor der Migration gelesen werden. Bereits toter Code wird sofort entfernt.
- **Pro GET-Route genau ein Render-Pfad:** `Inertia::render()`. Blade bleibt nur für E-Mails, das Inertia-Root-Template und die statische 503-Seite.
- **TDD:** Jede migrierte Route bekommt Feature-Tests (`assertInertia`), jede angefasste Mutation Tests für Redirect und Validierung. Frontend: Type-Check und Lint; manuelle Prüfung auf Staging.
- **Backend nur so weit anfassen, wie die Seite es braucht.** Übrige Modernisierung: #38.
- **Funktional vor schön:** Pro Seite nah am bisherigen Look und sauber strukturiert. Das Design kommt gebündelt in Phase 4.
- **Feature-Freeze:** Feature-Issues tragen das Label `nach-migration`. Kleine Features, die genau die gerade migrierte Seite betreffen, dürfen mit.

## Arbeitsweise

1. Issue wählen (Reihenfolge siehe Roadmap), Branch `mig/<nr>-kurzname` von `inertia-migration`.
2. TDD: zuerst ein Test, der fehlschlägt, dann die Umsetzung.
3. Vor dem PR: `vendor/bin/pint --test`, `php artisan test --compact`, `npm run build`, `npm run type-check`, `npm run lint`, `npm run format`. Die CI (`.github/workflows/ci.yml`) prüft dasselbe bei jedem PR nach `inertia-migration` und `master`.
4. PR nach `inertia-migration` mit `Closes #nr`, `/code-review`.
5. **Im selben PR** wird der Status in diesem Dokument aktualisiert (Roadmap und Routen-Matrix).
6. **Abnahme vor dem Merge:**
   - **Lokale Session:** Die Prüfungen aus Schritt 3 lokal ausführen, ohne auf die GitHub-CI zu warten. Paul nimmt lokal ab, der manuelle Staging-Deploy entfällt.
   - **Cloud-Session:** Wenn die CI grün ist, Workflow „Deploy Staging“ manuell auf dem Feature-Branch starten (immer `sync_db=yes`, frische Prod-Daten), Ende abwarten, Paul Bescheid geben. Paul nimmt am Handy ab.
7. Merge per Squash nach `inertia-migration`, Issue schließen. Jeder Push auf `inertia-migration` deployt Staging automatisch (mit Prod-Dump).

## Roadmap

| Phase | Inhalt | Issues | Status |
|---|---|---|---|
| 0 | Repo aufräumen, Cursor → Claude Code, PRs/Branches aufräumen | #26 | ✅ |
| 1 | Testsuite reparieren, Inertia-Testmuster, CI; Tooling-Entscheidungen (Boost-Update, Inertia v3, Wayfinder, Hooks, Larastan) | #27, #48 | ✅ |
| 2 | Review & Korrektur des Cursor-Stands, Tests für migrierte Seiten, Laravel Pint | #28, #58 | ✅ |
| 3 | Restliche Seiten ohne Live-Spiel | #29, #30, #73, #31, #32, #33 | ✅ |
| 4 | Design: bis zu 4 Spikes, Abstimmung, Rollout | #34, #35 | todo |
| 5 | Live-Spiel im neuen Design | #36 | todo |
| 6 | Legacy-Abbau, Spieleabend auf Staging, Go-Live | #37 | todo |
| danach | Backend-Modernisierung, Feature-Issues | #38, Label `nach-migration` | — |

#48 ist vor #28 abgeschlossen, damit die bestehenden Seiten nur einmal auf den finalen Stack gebracht werden.

Reihenfolge in Phase 3: #29 Benutzer → #30 Liste → #73 Spieler und Benutzer absichern → #31 Profil → #32 Runde → #33 Rest. Die Listen-Seite kommt vor dem Profil, weil dort die wiederverwendbaren Komponenten entstehen (Badges, Charts, Statistik-Tabellen, Archiv).

## Go-Live-Kriterien

1. Jede Route ist migriert oder bewusst gestrichen; Blade nur noch für E-Mails, das Inertia-Root-Template und die statische 503-Seite.
2. Tests, Type-Check und Lint sind grün.
3. Legacy-JS, Legacy-CSS und Legacy-Abhängigkeiten sind entfernt.
4. Mindestens ein echter Spieleabend auf Staging inklusive Live-Spiel ohne funktionale Fehler.
5. Rollback-Plan: `master` vor dem Merge taggen, DB-Backup, Datenmigrationen per wiederholbarem Neuberechnungs-Befehl.
6. Go-Live-PR #16 wird per Merge-Commit gemergt, damit die Historie und der Hash in `.git-blame-ignore-revs` erhalten bleiben.

## Routen-Matrix

Status: `todo` · `in_progress` · `✅` · `gestrichen`. Bei ✅-Zeilen aus der Cursor-Phase steht der Review noch aus (#28).

| Methode | URI | Route-Name | Action | Ziel-Page | Issue | Status | Hinweise |
|---|---|---|---|---|---|---|---|
| GET | `/`, `home` | `index`, `home` | `HomeController@index` | `Home/Index` | #28 | ✅ | Referenzimplementierung |
| GET | `runde/erstellen` | `rounds.create` | `RoundController@create` | `Rounds/Create` | #28 | ✅ | Nuxt-UI-Spike |
| GET | `rundenarchiv/{group?}` | `rounds.index` | `RoundController@index` | `Rounds/Index` | #28 | ✅ | Sortierung serverseitig (`sort`, `direction`), `UTable` mit `manualSorting` |
| GET | `listen` | `groups.index` | `GroupController@index` | `Groups/Index` | #28 | ✅ | |
| GET | `liste/erstellen` | `groups.create` | `GroupController@create` | `Groups/Create` | #28 | ✅ | nur Namensfeld |
| GET | `login`, `register`, `password/*`, `login/social/{socialiteUser}` | diverse | Auth-Controller | `Auth/*` | #28 | ✅ | `UAuthForm`, reCAPTCHA, Socialite-Attach; Nachbesserungen und Tests #54 |
| GET | `register/quick` | `register.quick` | `QuickRegisterController@show` | — | #54 | gestrichen | Schnellregistrierung ist ein Modal in `Rounds/Create`, nur noch `POST register/quick` |
| GET | `datenschutz`, `impressum`, `regeln` | gleichnamig | `StaticPageController` | `Static/*` | #28 | ✅ | |
| GET | `users/{user}/edit` | `users.edit` | `UserController@edit` | `Users/Edit` | #29 | ✅ | Tabs per `UTabs`; Avatar-Upload mit `vue-advanced-cropper` über `POST users/{user}/avatar` (`users.updateAvatar`); Button „Zurück zum Profil“ geht erst mit #31 |
| GET | `liste`, `liste/{group}` | `ewigeListe`, `groups.show` | `GroupController@show` | `Groups/Show` | #30 | ✅ | Rekorde aus Profilen (#39), `POST`/`PATCH` für beitreten/verlassen/schließen (#40), Tabs per URL-Hash |
| GET | `profil/{player}/{group?}` | `players.show` | `PlayerController@show` | `Players/Show` | #31 | ✅ | Tabs per URL-Hash, Charts per `Inertia::optional`, Archiv mit Spieler-Filter (`RoundArchive`); Profile versteckter und KI-Spieler nur für den Admin (`PlayerPolicy`) |
| GET | `runde/{round}` | `rounds.show` | `RoundController@show` | `Rounds/Show` | #32 / #36 | ✅ offline, Live-Spiel todo | offline inkl. Echtzeit per `RoundUpdated` (#32), Live-Tab bis Phase 5 nur mit Hinweis (#36); `Round.vue`, `LiveGame`, `RoundSettings` und `api/rounds/{round}/fetchData` bleiben bis dahin als Spezifikation |
| GET | `rounds/current` | `rounds.current` | `RoundController@current` | Redirect | #32 | ✅ | |
| GET | `report` | — | `ReportController@report` | — | #33 | gestrichen | ersetzt durch `stats:recalculate` und den Admin-Button in `Groups/Index` (`POST statistiken/neu-berechnen`) |
| GET | `listen/calculate`, `liste/calculate*`, `players/calculate*` | — | Group-/PlayerController | — | #33 | gestrichen | dito |
| — | Fehlerseiten ab 400 | — | `AppServiceProvider` | `Error` | #33 | ✅ | 419 als Toast zurück aufs Formular, 503 statisch in `errors/503.blade.php` |
| GET | alle unbekannten URLs | — | `Route::fallback` | `Error` | #33 | ✅ | im `web`-Stack, damit Angemeldete angemeldet bleiben; GET auf reine POST-URLs ergibt dadurch 404 statt 405 |
| — | Kommentare | — | `CommentController` | — | #33 | gestrichen | Tabelle bleibt bis #37; Wiedereinführung in #80 |
| GET | `test`, `testClient`, `mail` | — | `TestController` | — | #26 | gestrichen | Routen, Controller und Views entfernt |
| — | E-Mail-Verifizierung | — | `VerificationController` | — | #26 | gestrichen | Controller und `auth/verify.blade.php` entfernt |

**Keine Vollseiten, bleiben als Endpunkte:** `api/rounds/{round}/fetchData` (bis #36), `api/live/*`, Formular-POSTs/PATCHes/DELETEs, OAuth (`auth/redirect/*`, `callback/*`), Framework-Routen (`telescope`, `_debugbar`, `broadcasting/auth`).

**Ungenutzt, noch installiert:** `yajra/laravel-datatables-oracle`. Seit #31 greift nichts mehr darauf zu, der Ausbau des Pakets bleibt bei #45.

## Entscheidungen

| Datum | Entscheidung | Begründung |
|---|---|---|
| 2026-03 | Nuxt UI als UI-Framework für alle Inertia-Seiten | Spike an `rounds.create`: hohe Entwicklungsgeschwindigkeit für Formulare, Tabellen und Overlays; über das Theme anpassbar |
| 2026-03 | ESLint + Prettier + vue-tsc (ohne oxlint/oxfmt) | Pilotphase, etablierte Tools |
| 2026-03 | ~~Ziggy für benannte Routen im Frontend (`useAppRoute()`)~~ abgelöst durch Wayfinder (2026-10-28) | keine hart codierten URLs |
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
| 2026-10-28 | Laravel 13, Inertia v3, alle Abhängigkeiten auf aktuellem Stand | jede Seite nur einmal auf den finalen Stack bringen; Upgrade-Guides ohne relevante Brüche |
| 2026-10-28 | Wayfinder statt Ziggy | im Laravel-Ökosystem bleiben, typisierte Routen; siehe `docs/adr/0001-wayfinder-statt-ziggy.md` |
| 2026-10-28 | Kein Larastan | Tests und Type-Check reichen als Netz für die Migration |
| 2026-10-28 | Laravel Pint nach #28: einmaliger Formatierungs-Commit über alles, danach Pint im Hook | im Laravel-Ökosystem bleiben; erst nach #28, damit dessen Review-Diffs lesbar bleiben |
| 2026-10-28 | TypeScript 6.0 statt 7 | `typescript-eslint` unterstützt TS 7 noch nicht |
| 2026-10-28 | Claude-Code-Hook: Prettier und `php -l` nach jedem Edit | Formatierung und Syntax deterministisch statt per Regel in `CLAUDE.md` |
| 2026-10-30 | Links/Redirects auf noch nicht migrierte Seiten sofort als Inertia-Link bzw. `redirect()->route()` | kein Übergangscode, der später wieder angefasst werden muss; Kaputt-Sein bis zur Migration der Zielseite ist durch den Big Bang gedeckt (#52) |
| 2026-10-30 | Echtzeit-Aktualisierung der Offline-Runde (`RoundUpdated`) kommt mit #32, nicht erst in Phase 5; Echo verbindet sich nur auf Seiten, die lauschen | Bei jedem Spieleabend schauen mehrere Handys auf die Punkte; ohne Push wäre Staging zwischen #32 und #36 schlechter als Prod. Pusher vs. Reverb bleibt bei #36 (nur Transport) |
| 2026-10-30 | Meldungen nach Aktionen als Inertia-v3-Flash `toast` (`App\Http\Toast::success/error`), zentral als Toast an der App-Wurzel (`AppRoot.vue`); nur `success` und `error`; Validierungsfehler bleiben `errors` der Page | eine Stelle statt Anzeige pro Page; Inertia-Flash landet nicht im History-State, kommt also beim Zurück-Button nicht wieder; weitere Typen erst bei Bedarf (Live-Spiel) (#53) |
| 2026-10-30 | CI/Deploy-Runner fest auf `ubuntu-26.04`, Actions auf Node-24-Majors (`checkout@v7`, `setup-node@v7`, `cache@v6`), Node-Version aus `.nvmrc` (24) | `ubuntu-latest` wechselt ohnehin bis 19.11.2026 auf 26.04, fest gepinnt wechselt nichts unbemerkt; Node 26 ist erst seit 28.10.2026 LTS, der Server baut mit Node 24, CI soll dieselbe Version nutzen – Umstieg auf 26 zusammen mit dem Server |
| 2026-10-30 | Schnellregistrierung als Modal in „Neue Runde“ statt eigener Seite; der neue Spieler kommt per Inertia-Flash `addedPlayerId` zurück und wird direkt ausgewählt | Mit eigener Seite geht die bisherige Auswahl verloren (der Redirect ist ein neuer History-Eintrag, `useRemember` hilft nicht); das Modal mit `preserveState` erhält sie ohne Übergangscode (#54) |
| 2026-10-30 | Schnellregistrierte Benutzer bekommen eine Platzhalter-E-Mail (`schnellregistrierung-<uuid>@ewigeliste.invalid`) statt einer Pflichtangabe | Die Personen loggen sich realistisch nie ein; erfundene Adressen blockierten sonst später die echte. Login bei Bedarf durch Nachtragen der echten E-Mail (#54) |
| 2026-10-30 | Rundenarchiv fest mit 20 Runden pro Seite; die Auswahl „15 / 30 / Alle“ von `master` entfällt | bewusste Vereinfachung; Abweichung von `master`, kein vergessener Punkt (#55) |
| 2026-10-30 | „Letzte Runden“ auf Home: Runden mit einem Spiel aus den letzten 24 h, sortiert nach dem neuesten Spiel (vorher `updated_at` der Runde) | Das Bearbeiten einer alten Runde (Spieler, Listen) holte sie sonst wieder auf die Startseite (#55) |
| 2026-10-30 | `GamesChartSeries` bleibt MySQL-spezifisch (`DATE_FORMAT`, `WEEKDAY`); getestet werden nur Leerfall und Deferred-Prop | Die Logik ändert sich praktisch nicht mehr; ein datenbankneutraler Umbau lohnt den Aufwand nicht (#55) |
| 2026-10-30 | Seitenwechsel vorerst ohne Instant Visits, Skeletons, Prefetch und persistentes Layout; nur die Fortschrittsleiste ist orange; Deferred nur für langsame, in der ersten Ansicht entbehrliche Daten | Am Handy fühlt es sich schnell genug an, der Aufwand lohnt nicht. Bedarf wird nach dem Spieleabend auf Staging geprüft (#37), Erkenntnisse stehen in #57 |
| 2026-10-30 | Alle Textdateien fest mit LF (`.gitattributes`, `.editorconfig`), auch unter Windows | Pint und Prettier verlangen LF; mit `core.autocrlf=true` wären `pint --test` und `npm run format` unter Windows sonst dauerhaft rot, während die CI grün ist (#58) |
| 2026-10-30 | #58 ausnahmsweise per Rebase and merge, Go-Live-PR #16 per Merge-Commit | Der Formatierungs-Commit muss einzeln mit stabilem Hash erhalten bleiben, damit `.git-blame-ignore-revs` greift; Squash würde ihn mit anderen Änderungen vermischen (#58) |
| 2026-10-30 | Abnahme hängt von der Session ab: lokal ohne Warten auf die CI und ohne manuellen Staging-Deploy, in der Cloud-Session wie bisher mit beidem | Lokal ist schneller, die CI läuft trotzdem bei jedem PR mit (siehe Arbeitsweise, Schritt 6) |
| 2026-10-30 | Ohne Avatar ist `avatar_path` `null`, `UAvatar` zeigt dann die Initialen; `default.jpg` ist gelöscht | Nuxt UI bringt das Verhalten mit. Legacy-Seiten zeigen bei Personen ohne Avatar bis zu ihrer Migration ein kaputtes Bild, das deckt der Big Bang ab (#29) |
| 2026-10-30 | Avatar-Upload als normales Inertia-Formular (`users.updateAvatar`, Redirect, Toast) statt JSON-Endpunkt; Zuschnitt im Browser auf höchstens 800 px; Dateiname aus Benutzer-ID und Zeitstempel | Ein Muster für alle Formulare der Seite. Name im Dateinamen konnte durch Sonderzeichen die URL brechen, alte Dateien bleiben unverändert (#29) |
| 2026-10-30 | Standardlisten: Das Frontend schickt nur die IDs der gewählten Profile, der Server ändert nur eigene, offene Profile außerhalb der Ewigen Liste | Vorher prüfte `updateListen` den Besitzer nicht und vertraute `closed`/`group_id` aus dem Request, jeder konnte fremde Standardlisten umschalten (#29) |
| 2026-10-30 | Rekorde werden beim Anzeigen aus den Profilen abgeleitet, Listenstatistiken liegen in den vorhandenen Spalten `totalGames`, `pointsPerGame`, `gamesPerRound` und werden vom Job befüllt; das JSON aus `records`/`stats` entfällt | Rekorde sind nur Min/Max über gespeicherte Profile und wachsen nicht mit den Spielen, Listenstatistiken schon (Prod: rund 30.000 Spiele). Keine Datenmigration des JSON; die Spalten bleiben bis #37 unangetastet, damit ein Rollback auf `master` sie noch vorfindet (#30, #39) |
| 2026-10-30 | Austreten nur ohne Runde in der Liste, nicht als letztes Mitglied, nicht aus der Ewigen Liste, nicht bei geschlossener Liste; geschlossene Listen sperren auch das Beitreten; die Ewige Liste kann nicht geschlossen werden | Bisher widersprachen sich Button und Endpunkt, und eine geschlossene Ewige Liste würde jede neue Runde blockieren. Der alte Text „Bestehende Runden können nicht verändert werden“ entfällt, weil der Code das nie erzwungen hat (#30) |
| 2026-10-30 | Mitgliedertabelle nur noch mit Sortierung; Suche, Blättern und gemerkter Sortierzustand von `master` entfallen | einfachste Variante; die Seite wird nach der Migration ohnehin weiter ausgebaut (#30) |
| 2026-10-30 | Das Listen-Icon der Navigation führt auf die Ewige Liste, alle Listen sind von dort verlinkt | meist wird nur die Ewige Liste aufgerufen (#19) |
| 2026-10-30 | Tabs der Listen-Seite stehen im URL-Hash, aber ohne eigenen History-Eintrag; das Chart lädt erst, wenn der Tab offen ist (`Inertia::optional` + `WhenVisible`) | Der Zurück-Button verlässt die Seite, statt durch die Tabs zu springen (Abweichung von `master`); die Chart-Abfrage läuft auf Prod über rund 30.000 Spiele und soll nicht bei jedem Aufruf anfallen (#30) |
| 2026-10-30 | `groups.pointsPerGame` und `gamesPerRound` werden vor dem Befüllen auf `decimal(6,1)` erweitert | Auf Prod noch `float(3,1)` (maximal 99,9), Liste 9 hat 200 Spiele pro Runde. Lokal und in der CI nicht sichtbar: Laravel 13 ignoriert die Nachkommastellen in `float()`, SQLite kennt keinen Wertebereich |
| 2026-10-30 | Jeder Spieler gehört zu genau einem Benutzer: `PlayerRegistration` legt Spieler, Benutzer und Mitgliedschaft in der Ewigen Liste in einer Transaktion an, `users.player_id` ist eindeutig. Die Migration bricht bei Verstößen mit den IDs ab; dieses Ticket läuft trotz lokaler Session über Staging | „Jeder Spieler hat einen Benutzer“ ist per Datenbank nicht erzwingbar (MySQL kann Fremdschlüssel nicht aufschieben). Bereinigt wird gezielt statt automatisch; ob die Prod-Daten passen, zeigt nur der Lauf mit Prod-Dump (#73) |
| 2026-10-30 | Versteckte Spieler sperrt eine Middleware im `web`-Stack bei jedem Request (Logout, Redirect auf den Login, Toast), der Admin ist ausgenommen. Keine eigene Fehlermeldung im `LoginController` | Deckt mit einem Weg Passwort-Login, Socialite, Passwort-Reset, laufende Sessions und „Angemeldet bleiben“ ab; die Meldung erscheint nur bei korrektem Passwort und verrät Fremden nichts. Vor dem Prod-Deploy prüft Paul die Liste `WHERE hide = 1` (#73) |
| 2026-10-30 | Profile versteckter Spieler und von KI-Spielern sind nur für den Admin sichtbar (`PlayerPolicy`, 403). In Tabellen, Rekorden und Abzeichen bleiben die Namen stehen, die Resources liefern dann `path: null` und der Name erscheint ohne Link | Betrifft nur das Profil, nicht die Auswertung der Liste; ein Link auf einen 403 wäre eine Sackgasse. Die Rundenseite zieht mit #32 nach (#31) |
| 2026-10-30 | Profil-Charts wie auf der Listen-Seite per `Inertia::optional` + `WhenVisible`, Berechnung in `ProfileChartSeries`, `charts/profile/{profile}` gelöscht | Ein Muster, und die Sperre gilt ohne eigenen Endpunkt. Die Berechnung lädt alle Spiele des Spielers; ob das auf Prod reicht, zeigt die Abnahme auf Staging. Gegenüber `master` korrigiert: Tage ohne Spiel zählten das nächste Spiel schon mit, beim Ausdünnen konnte der neueste Punkt fehlen (#31) |
| 2026-10-30 | Profil zeigt immer alle fünf Tabs, leere mit Hinweistext; die Listen-Seite blendet leere Tabs aus | Der Listenwechsel im Profil behält den Tab, er darf nicht wegspringen, weil die neue Liste keine Abzeichen hat (#31) |
| 2026-10-30 | Der Begriff bleibt überall „Abzeichen“, „Monatsrekorde“ aus der alten Profil-Seite entfällt | Eine Sache, ein Name; „Rekord“ ist der Bestwert einer Liste (#31) |
| 2026-10-31 | Ändert ein anderes Handy die Runde, während der Eintragen-Dialog offen ist, schließt und leert er sich; ein Warn-Toast bleibt stehen, bis er weggeklickt wird. Der Server lehnt Gewinner ab, die in diesem Spiel aussetzen | Eine stehen gebliebene Auswahl ergab sonst ein doppeltes Spiel oder bei 5 bis 7 Spielern falsche Punkte. Der Toast entsteht im Browser, `App\Http\Toast` bleibt bei `success` und `error`. Gleichzeitiges Speichern im selben Moment bleibt möglich und wird über „Letztes Spiel löschen“ behoben (#32) |
| 2026-10-31 | Echo verbindet sich per dynamischem Import nur auf `Rounds/Show` (`useRoundUpdates`, vorhandenes `laravel-echo`, kein `@laravel/echo-vue`). Zusätzlich lädt die Seite neu, sobald sie wieder sichtbar wird; neue Tabellenzeilen leuchten kurz grün | Gesperrte Handys verpassen Pushes, das fiel auf Spieleabenden immer wieder auf. Die Vue-Schicht bringt für eine Seite wenig; Entscheidung dazu und zu Pusher vs. Reverb bleibt bei #36 (#32) |
| 2026-10-31 | Sitzordnung im Eintragen-Dialog kommt nur noch vom Server: Wer aktiv ist, steht unten, wer aussetzt, beginnt beim nächsten aktiven Sitz. Außerhalb der Runde gilt die Sitzreihenfolge | Auf `master` widersprachen sich Browser und Server für Aussetzende, nach dem eigenen Eintragen war die Anordnung um einen Platz verschoben (#32) |
| 2026-10-31 | Listen einer Runde: Die Ewige Liste und geschlossene Listen der Runde bleiben immer erhalten und werden gesperrt angezeigt, fremde geschlossene Listen werden nicht angeboten. Der Server prüft das; neu berechnet werden auch entfernte Listen | Bei einer alten Runde ist wichtig zu sehen, wo sie gezählt hat (Urlaubslisten). `master` blendete geschlossene Listen aus, vertraute dem Request und ließ die Statistik entfernter Listen veraltet (#32) |
| 2026-10-31 | `changeDates` und `charts/round/{round}` gelöscht, Spiel-Endpunkte auf `games.store` und `games.destroy` umgezogen; Live-Runden sperren manuelles Eintragen und Löschen auf dem Server; ein Push-Fehler lässt das Eintragen nicht mehr scheitern (`ShouldRescue`) | `changeDates` hatte seit Jahren keine Oberfläche und keine Berechtigungsprüfung. Nach einem Push-Fehler war das Spiel schon gespeichert, ein erneutes Absenden hätte es verdoppelt (#32) |
| 2026-10-31 | Lokal läuft die Queue über `database` mit einem Worker im eigenen Terminal-Tab (`queue:listen --tries=1`), Tests bleiben auf `sync`; Prod bleibt unverändert. Die Statistik-Jobs setzen `queued` vor der Berechnung zurück, `failed_jobs` kommt per Migration dazu | Mit `sync` wartete das Eintragen lokal rund 40 Sekunden. `deferred` hilft unter Herds `php-cgi` voraussichtlich nicht. Ohne `failed_jobs` verdrängt der Fehler beim Protokollieren die eigentliche Ursache |
| 2026-10-31 | Kommentare gestrichen, die Tabelle bleibt bis #37; der Abschnitt in der Datenschutzerklärung entfällt. Wiedereinführung in #80 | Auf `master` war das Formular nicht mehr erreichbar. Rollback auf `master` findet die Tabelle noch vor (#33) |
| 2026-10-31 | Ein Befehl `stats:recalculate` stellt Jobs pro Profil und pro Listenmonat in die Queue, der Admin startet ihn auch per Button in `Groups/Index`. Nachtlauf entfällt, stattdessen läuft der Befehl am 1. jedes Monats. Der Staging-Deploy startet ihn nach `sync_db`, auf Prod einmalig beim Go-Live (#37) | Ein großer Job liefe über das Zeitlimit des Workers; Statistiken neuer Spiele warten während eines Laufs hinter den eingestellten Jobs. Jobs nach jedem Spiel halten die Zahlen aktuell, nur „in diesem Monat“ veraltet zum Monatswechsel ohne neues Spiel; „Spiele pro Tag“ driftet bis zum nächsten Lauf leicht. Der Prod-Dump enthält die Listenstatistik noch nicht in den neuen Spalten (#33) |
| 2026-10-31 | Eine Page `Error` für jeden Status ab 400. Ab 500 ohne geteilte Daten und Navigation, bei `APP_DEBUG` bleibt die Debug-Seite; JSON bleibt unverändert. 419 führt mit Toast aufs Formular zurück. 503 bleibt eine statische Blade-Datei ohne Vite | Die geteilten Daten brauchen die Datenbank, die bei einem 500er oft die Ursache ist. `artisan down` läuft im Deploy während des Asset-Builds, eine Inertia-503-Seite bliebe weiß (#33) |

## Offene Fragen

- **Pusher vs. Laravel Reverb** für das Live-Spiel; zusammen mit `@laravel/echo-vue`. Entscheidung zu Beginn von Phase 5 (#36), ADR-Kandidat.

## Learnings

- Die Routen-Matrix kann von der Realität abweichen: Vor der Umsetzung immer die echte Blade lesen und prüfen, welche Variablen das Template wirklich nutzt. Ungenutzte Controller-Daten werden entfernt, nicht als Prop weitergereicht (Beispiel `groups.create`).
- Mit `UTable` und `paginate()` liegt nur die aktuelle Seite im Browser: Sortieren und Filtern gehört ins Backend (Sortierspalten auf eine Whitelist, stabiler Tie-Breaker), im Frontend `manualSorting`.
- `Resource::collection()` nutzt `AnonymousResourceCollection` mit eigenem `$wrap = 'data'`; das `$wrap = null` der Resource greift dort nicht. Als Prop deshalb `->resolve($request)` anhängen (#30).
- Inertia-Links auf Seiten, die noch Blade sind, liefern eine HTML-Antwort statt einer Inertia-Antwort. Wegen des Big Bangs nehmen wir das bewusst in Kauf.
- Referenz für „Wird das noch genutzt?“ ist `master`, nicht der Branch: In der Cursor-Phase wurden Legacy-Komponenten auf dem Branch bereits umgebaut oder gelöscht. Beispiel: `vue-slicksort` und `vue-scrollto` sind auf dem Branch ungenutzt, auf `master` hingen daran Drag-and-drop der Spieler und das Scrollen zum Suchfeld in `CreateRound.vue` (#52). Für #30–#32 gilt das besonders: Die Charts (Chart.js v4) und `rounds/CreateGame.vue` (Modal ohne jQuery) wurden auf dem Branch schon umgebaut (#55).
- Legacy-Vue-Komponenten werden in `resources/js/app.js` per Glob global registriert. Ob eine Komponente noch genutzt wird, zeigt nur ein Grep nach ihrem Tag (PascalCase und kebab-case) in Blade und anderen Legacy-Komponenten, kein Import.
- Boost-Guidelines und -Skills nach Paket-Updates mit `php artisan boost:update` neu erzeugen, nicht von Hand pflegen. Die Guidelines landen über `config/boost.php` in `AGENTS.md` (von `CLAUDE.md` importiert), die Skills in `.claude/skills/`.
- PHPUnit 12 erkennt `/** @test */` nicht mehr; ohne `#[Test]` laufen solche Tests stillschweigend nicht. Die alte Suite lief deshalb mit 5 statt 30 Tests.
- `assertSee` ohne `assertOk()` ist trügerisch: Eine 500er-Seite enthält den gesuchten Text oft im Stacktrace. Mehrere Blade-Tests waren so nur scheinbar grün.
- Factories speichern Namen roh wie die App selbst. Ein `e()` in der Factory erzeugt doppelt escapte Daten und flaky Tests bei Namen mit Apostroph.
- Unter Windows verschluckt der `composer`-Wrapper `^` in Argumenten (cmd-Escape): Versionen in `composer.json` direkt setzen statt `composer require "pkg:^1.0"`.
- Die Ewige Liste (ID 1) legt `Tests\TestCase` in jedem Test mit `RefreshDatabase` an. Der erste per Factory angelegte Benutzer hat ID 1 und ist damit Admin (`Gate::before`); Berechtigungstests brauchen einen zweiten Benutzer.
- Benutzer- und Spieler-IDs nie gleichsetzen: Jede `GroupFactory` legt einen eigenen Spieler (`created_by`) an, die IDs laufen also auseinander. Ältere Tests schickten Benutzer-IDs als `players` und waren nur zufällig grün (#51).
- Eine Migration kann Funktionen still verlieren, die berechnet, aber nie gerendert werden (Listenauswahl in `rounds.create`, #51). Beim Review die Legacy-Kerninteraktionen einzeln gegen die neue Page abhaken.
- Laravel-Auth-Traits (`SendsPasswordResetEmails`, `ResetsPasswords`) setzen Session-`status`. Für Toasts `sendResetLinkResponse`/`sendResetResponse` im Controller überschreiben (#53).
- `useRemember` speichert im History-State des aktuellen Eintrags. Ein Formular-POST mit Redirect zurück auf die Seite erzeugt einen neuen Eintrag: Der gemerkte State ist dort leer. Zustand über einen Umweg erhalten geht nur ohne Navigation (Modal mit `preserveState`) (#54).
- `Resource::collection()` direkt als Prop landet trotz `$wrap = null` unter `data`, weil die Collection den Wrapper der Basisklasse nutzt. Immer `->resolve(request())` anhängen (#29).
- `herd workspace` zweigt von `master` ab, nicht vom aktuellen Branch, und verknüpft mit `origin/master`. Danach den frischen Branch auf `origin/inertia-migration` umsetzen und die Verknüpfung lösen (#29).
- Wayfinder erzeugt seine Dateien beim `npm run build` aus den geladenen Routen, also auch aus einem vorhandenen `route:cache`. Die Deploy-Workflows leeren deshalb vor dem Build die Caches; sonst fehlen neue Routen, obwohl die CI ohne Cache grün ist.
- Der Pint-Hook entfernt nach jedem Edit ungenutzte Imports. Einen neuen `use` daher zusammen mit oder nach seiner Verwendung einfügen, sonst ist er beim nächsten Edit schon wieder weg (#31).
- `WhenVisible` lädt pro Komponenten-Instanz nur einmal. Bleibt der State bei einem Listenwechsel erhalten (`preserveState`), braucht die Komponente einen `key` je Liste, sonst bleibt das Chart leer (#31).
- `php artisan wayfinder:generate` ohne `--path` schreibt nach `resources/js/` statt nach `resources/js/inertia/`. Generiert wird über `npm run build`, das Vite-Plugin kennt den richtigen Pfad (#32).
- Das Inertia-Layout hat kein `csrf-token`-Meta-Tag. Echo braucht für Presence-Channels deshalb den Token aus dem `XSRF-TOKEN`-Cookie als `X-XSRF-TOKEN`-Header (`useRoundUpdates`, #32).
