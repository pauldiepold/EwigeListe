# Inertia Migration Plan

## Ziel und Abgrenzung

- Migration von Blade-getriebener UI zu Inertia + Vue 3 in kontrollierten Schritten.
- Backend bleibt stabil; Controller werden nur fuer die Response-Schicht angepasst.
- Dieser Plan definiert bewusst nur Vorgehen und Tasks, keine seitenweise Todo-Liste.
- Start erfolgt mit genau einer Pilotseite: Homepage.

## Verbindliche Leitplanken

- Parallelbetrieb ist Standard: Blade und Inertia laufen waehrend der Migration nebeneinander.
- Pro Route gilt genau ein Render-Pfad: `view()` oder `Inertia::render()`, niemals gemischt.
- Bei Unsicherheit werden offene Punkte aktiv geklaert, nichts wird stillschweigend angenommen.
- Funktionale Regressionen sind nicht akzeptabel; visuelle Unsauberkeiten sind temporaer tolerierbar.

## Zielstruktur fuer neuen Inertia-Code

Nur neuer Migrationscode geht in eine klar abgegrenzte Struktur:

```text
resources/js/inertia/
  app.ts
  Pages/
    Home/
      Index.vue
  Layouts/
    AppLayout.vue
  Components/
  composables/
  types/
    inertia.d.ts
```

Bestehender Altcode bleibt zunaechst unangetastet und klar erkennbar ausserhalb dieses Bereichs.

## Tooling-Strategie (neu vs. alt strikt getrennt)

- TypeScript, ESLint, Prettier und `vue-tsc` werden eingefuehrt.
- Linting/Type-Checking wird in der Einfuehrungsphase nur auf neue Inertia-Ordner angewendet.
- Alter Legacy-Code wird explizit vom Linting ausgeschlossen, bis dessen Migration startet.
- Build muss fuer das Gesamtprojekt weiter funktionieren; Qualitaets-Gates sind fuer neue Ordner verpflichtend.
- Benannte Laravel-Routen im neuen Frontend ueber **Ziggy**: Composer-Paket `tightenco/ziggy`, npm `ziggy-js`, Inertia-Shared-Prop `ziggy` in `HandleInertiaRequests`, Aufrufe ueber Composable `resources/js/inertia/composables/useAppRoute.ts`.

## Abhakbarer Task-Plan

### Phase A - Grundlagen fuer Pilotseite

- [x] Inertia-Basis fuer Frontend finalisieren (Entry, Root-Template, Middleware, Shared Data als Minimalset).
- [x] Zielstruktur `resources/js/inertia/*` anlegen und als einziger neuer Frontend-Bereich festlegen.
- [x] Tooling aus modernem Vue-Referenzprojekt uebernehmen (TS, ESLint, Prettier, vue-tsc).
- [x] Lint-/Type-Check-Skripte auf neue Inertia-Ordner begrenzen; Legacy explizit ausnehmen.
- [x] Smoke-Test-Route/Page bereitstellen, damit Setup vor Start der Pilotmigration validiert ist.

Ergebnis: Technische Basis steht, ohne das Legacy-Frontend global zu refactoren.

Status: Abgeschlossen und validiert (`npm run type-check`, `npm run lint:fix`).
Tooling-Entscheidung fuer Pilotphase: ESLint + Prettier + vue-tsc (ohne oxlint/oxfmt).

### Phase B - Homepage als Pilotmigration

- [x] Homepage-Route auf Inertia umstellen (Controller mit `Inertia::render()`).
- [x] Bestehende Homepage-Daten in sinnvolle Inertia-Props ueberfuehren und typisieren.
- [x] Homepage als `Pages/Home/Index.vue` in neuer Struktur implementieren.
- [x] Homepage gegen Kerninteraktionen pruefen (Laden, Navigation, Datenanzeige, Flash/Auth falls relevant).
- [x] Altpfad der Homepage klar markieren (entfernt oder als Legacy-Rest dokumentiert).

Ergebnis: Eine reale Seite laeuft stabil im neuen Muster und dient als Referenz fuer alle Folgeschritte.

Status: done.
- Neuer Pfad: `HomeController@index` → `Inertia::render('Home/Index')` → `Pages/Home/Index.vue`
- Legacy: `resources/views/home/home.blade.php` als Legacy markiert, wird nicht mehr gerendert.
- Architektur: Controller minimal, Mapping in `Http/Resources/`, Typen in `types/home.ts`, Chart als Composable mit `fetch`.
- Build, Lint und Type-Check laufen sauber.

### Phase C - UI-Framework-Entscheidung (nach funktionierender Homepage)

- [x] Kleinen Spike auf 1-2 realen UI-Ausschnitten durchfuehren (z. B. Formular + Tabelle/Overlay).
- [x] Bewertung nach festen Kriterien: Integrationsaufwand, Entwicklungsgeschwindigkeit, Wartbarkeit.
- [x] Entscheidung dokumentieren (Nuxt UI oder kein neues Framework) und als Standard festhalten.

Ergebnis: Technologiewahl ist validiert, bevor weitere Seiten migriert werden.

Status: done (Spike: `RoundController@create` → `Pages/Rounds/Create.vue` mit Nuxt UI; Theme primary/secondary auf App-Palette gemappt; Dark-Mode-Basis im Layout).

**Entscheidung:** Nuxt UI bleibt **Standard fuer migrierte Inertia-Seiten** in der Migrationsphase.

**Bewertung (kurz):**

| Kriterium | Einschaetzung |
|-----------|----------------|
| Integrationsaufwand | Akzeptabel; einmal Vite/Inertia korrekt verdrahten (ESM, `router: 'inertia'`), danach reproduzierbar. |
| Entwicklungsgeschwindigkeit | **Hoch** fuer Formulare, Feedback, Overlays, konsistente Patterns — weniger Custom-CSS/JS pro Seite. |
| Wartbarkeit | **Gut**, solange man sich an Konventionen haelt (Theme, `U*`-Komponenten); weniger eigene UI-Hilfsschicht als bei reinem Tailwind-Basteln. |
| Optik „out of the box“ | Geschmackssache; **bewusst nicht** Ziel der Migrationsphase (Leitplanke: funktional, spaeter Design-Pass). |
| Anpassbarkeit | **Stark** ueber Theme/Variants; spaeterer Feinschliff (Typo, Abstaende, Custom-Slots) ohne Framework-Wechsel moeglich. |

**Risiko / Mitigation:** Optik und „Generic-UI“-Gefuehl — nach Migration gezielter **UI-/Design-Skill-Pass** (Theme verfeinern, ggf. wenige eigene Wrapper-Komponenten), nicht waehrend jeder Seitenmigration.

**Nicht-Ziele in Phase C:** Pixel-perfektes Branding; vollstaendiger Abbau von `bootstrap-compat.css` (folgt schrittweise mit Blade-Migration).

### Phase D - Go/No-Go fuer eigentliche Seitenmigration

- [x] Pilot-Review: Muster, Folder-Struktur, Tooling-Scope und UI-Entscheidung final bestaetigen.
- [ ] Erst danach Start der eigentlichen Route-fuer-Route-Migration.
- [ ] Migration weiterhin in kleinen, verifizierbaren Schritten mit Status `todo`, `in_progress`, `done`.

Ergebnis: Skalierbarer, risikoarmer Startpunkt fuer die komplette Migration.

Status: Punkt 1 erledigt.
- Layout-Basis fuer Inertia vereinheitlicht (`Layouts/AppLayout.vue`) mit bestehender Top-Leiste + Icon-Navigation als Uebergangsstandard.
- Dark-Mode-Schalter in Footer verschoben, um Header/Navi auf Navigation zu fokussieren.
- Struktur-Review: Zielstruktur und Trennung (Legacy vs. `resources/js/inertia/*`) sind fuer den Migrationsstart tragfaehig.

## Definition of Done fuer die Pilotphase

- Homepage ist als Inertia-Page produktiv nutzbar.
- Build ist stabil; Lint und Type-Check laufen fuer den neuen Inertia-Bereich sauber durch.
- Legacy-Bereich bleibt klar getrennt und wird nicht versehentlich in neue Lint-Regeln gezogen.
- UI-Framework-Entscheidung ist getroffen und dokumentiert.

## Offene Fragen, die vor Umsetzung immer aktiv geklaert werden

- Welche Homepage-Daten sind zwingend initial serverseitig zu liefern und welche duerfen spaeter lazy geladen werden?
- Welche Shared-Data-Felder sind global erforderlich (auth, locale, flash, feature flags)?
- Welche Legacy-Bereiche muessen waehrend der Pilotphase garantiert unveraendert bleiben?

---

## Routen-Matrix (Vollseiten-Migration Blade → Inertia)

**Quelle:** `php artisan route:list --json` plus Controller-Scan (`view()` vs. `Inertia::render()`). Stand fuer Agent und Team als Arbeits-Backlog.

**Pflicht vor Umsetzung einer Zeile:** Zugehörige **Blade-Datei** lesen und prüfen, welche Variablen das Template **tatsächlich** nutzt. Controller und Matrix beschreiben manchmal mehr (z. B. geladene Collections ohne Blade-Verwendung) oder eine veraltete UI-Idee — das ist **kein** Migrationsumfang, bis es in Blade/Verhalten existiert. Nach Klärung die Matrix-Zeile („Erwartete Probleme / Hinweise“, Aufwand) anpassen, damit der Backlog nicht irreführt. Referenz: `groups.create` (nur `name`-Formular; ungenutztes `allPlayers` im Controller entfernt).

**Legende — Aufwand (grobe PT-Schätzung):**

| Stufe | Bedeutung |
|-------|-----------|
| S | ca. 0,5–1 PT: wenige Props, wenig Interaktion, Muster wie Pilot |
| M | ca. 1–3 PT: Formulare, Tabellen, mehrere Resources |
| L | ca. 3–7 PT: viel UI-Logik, Legacy-Vue-Einbindung, Live-Runde, viele Teilkomponenten |

**Legende — Komplexitaet:**

| Stufe | Bedeutung |
|-------|-----------|
| niedrig | klar abgegrenzte Daten, wenig JS-Legacy |
| mittel | Policies, mehrere Modelle, Charts/Fetch daneben |
| hoch | eingebettetes altes Vue (SPA-artig), DataTables, Echtzeit, grosse Zustandsflächen |

**Leitregel:** Pro **GET-Route, die eine HTML-Vollseite** ausliefert, gilt genau ein Render-Pfad (`Inertia::render` **oder** Blade), siehe Plan oben. **POST/PATCH/DELETE** und JSON-Endpunkte bleiben typischerweise Controller-Logik mit Redirect/JSON; sie werden indirekt migriert, wenn die zugehörige Inertia-Seite Formulare/Aufrufe umstellt.

### Tabelle: Routen mit Controller-Umstellung auf `Inertia::render` (Backlog)

| Methode | URI | Route-Name | Action | Status | Ziel-Page (Vorschlag) | Aufwand | Komplexitaet | Erwartete Probleme / Hinweise |
|---------|-----|------------|--------|--------|------------------------|---------|--------------|------------------------------|
| GET | `/` | `index` | `HomeController@index` | **done** | `Home/Index` | — | — | Referenzimplementierung. |
| GET | `home` | `home` | `HomeController@index` | **done** | `Home/Index` | — | — | Alias derselben Page. |
| GET | `runde/erstellen` | `rounds.create` | `RoundController@create` | **done** | `Rounds/Create` | — | — | Nuxt UI, Spike-Seite. |
| GET | `rundenarchiv/{group?}` | `rounds.index` | `RoundController@index` | **done** | `Rounds/Index` | — | — | Gruppe optional, Default ID 1 (Ewige Liste); Laravel-Pagination (`?page=`); **Sortierung serverseitig** (`?sort=date|games|online`, `?direction=asc|desc`), Nuxt UI `UTable` mit `manualSorting`; Prop `archiveSort`. `rounds.archiveTable` bleibt für Blade-Tabs Gruppe/Profil. |
| GET | `runde/{round}` | `rounds.show` | `RoundController@show` | todo | `Rounds/Show` | **L** | **hoch** | Blade ist nur Shell; Kern ist Legacy-`<round>`-Vue mit API/WebSockets. Migration = grösster Brocken: Props/Resources alignen, Live-Spiel, Kommentare, Spiele — evtl. schrittweise (Shell Inertia, Kind zuerst portieren). |
| GET | `listen` | `groups.index` | `GroupController@index` | **done** | `Groups/Index` | M | mittel | Listen mit Counts; Navigation zu Detail. |
| GET | `liste/erstellen` | `groups.create` | `GroupController@create` | **done** | `Groups/Create` | — | — | Blade nur Namensfeld; ungenutztes `allPlayers`-Query entfernt. |
| GET | `liste` | `ewigeListe` | `GroupController@show` | todo | `Groups/Show` | M | mittel | Default-Gruppe (wie `show` ohne Parameter); gleiche Page-Komponente wie `groups.show`. |
| GET | `liste/{group}` | `groups.show` | `GroupController@show` | todo | `Groups/Show` | **L** | **hoch** | Viele Relationen, Badges, Statistiken, eingebettete Charts (`charts.home` als JSON); tech-debt `group`-JSON beachten (`docs/tech-debt/group-positional-json.md`). |
| GET | `profil/{player}/{group?}` | — | `PlayerController@show` | todo | `Players/Show` | **L** | **hoch** | Ähnliche Datenfülle wie Gruppen-Seite; optionale Gruppe; Badges/Charts. **Route benennen** (Ziggy): empfohlen `players.show`. |
| GET | `users/{user}/edit` | `users.edit` | `UserController@edit` | todo | `Users/Edit` | M | mittel | Mehrere Formularbereiche (Name, Mail, Passwort, Listen); PATCH-Routen bleiben, Inertia `useForm` / Nuxt UI Forms. |
| GET | `login` | `login` | `LoginController@showLoginForm` | todo | `Auth/Login` | M | mittel | Laravel-Trait liefert aktuell Blade; auf `Inertia::render` umstellen (eigene `showLoginForm`-Methode), Socialite-Flow (`socialiteUserId`) erhalten. |
| GET | `register` | `register` | `RegisterController@showRegistrationForm` | todo | `Auth/Register` | M | mittel | reCAPTCHA-Regeln aus `RegisterController@validator` in Page/Request spiegeln; Validierungs-Messages. |
| GET | `password/reset` | `password.request` | `ForgotPasswordController@showLinkRequestForm` | todo | `Auth/ForgotPassword` | S | niedrig | Standard-Flow. |
| GET | `password/reset/{token}` | `password.reset` | `ResetPasswordController@showResetForm` | todo | `Auth/ResetPassword` | S | niedrig | Token als Prop. |
| GET | `register/quick` | `register.quick` | `QuickRegisterController@show` | todo | `Auth/QuickRegister` | S | niedrig | Auth-pflichtig; kleine Seite. |
| GET | `login/social/{socialiteUser}` | `auth.registerOrAttach` | `SocialiteController@showView` | todo | `Auth/RegisterOrAttach` | M | mittel | Entscheidung Registrierung vs. Account verknüpfen; nach Auth-Flows konsolidieren. |
| GET | `datenschutz` | `datenschutz` | `ViewController` | todo | `Legal/Datenschutz` oder `Sonstiges/Datenschutz` | S | niedrig | Heute `Route::view`; Inhalt kann als statische Vue-Page oder weiter serverseitig mit minimalem Wrapper migriert werden. |
| GET | `impressum` | `impressum` | `ViewController` | todo | `Legal/Impressum` | S | niedrig | wie Datenschutz. |
| GET | `regeln` | `regeln` | `ViewController` | todo | `Legal/Regeln` | S | niedrig | wie Datenschutz. |
| GET | `report` | — | `ReportController@report` | optional | `Admin/Report` | M | mittel | Nur `admin`-Middleware; kann nach Kern-App oder ganz zuletzt. |
| GET | `test` | — | `TestController@test` | optional | — | S | niedrig | Dev/Admin; Migration nur bei Bedarf. |
| GET | `testClient` | — | `TestController@client` | optional | — | S | niedrig | wie `test`. |

### Routen ohne Vollseiten-`Inertia::render` (typisch unverändert oder nur angepasst)

Diese Endpunkte liefern **keine** klassische Blade-Vollseite für die Migration, bleiben aber relevant für Inertia-Seiten (Form-Targets, `fetch`, DataTables, Redirects):

| Kategorie | Beispiele (URI) | Hinweis |
|-----------|-----------------|--------|
| JSON / Array | `charts/home/{group}`, `charts/round/{round}`, `charts/profile/{profile}` | Daten für Charts; von Inertia per `fetch` nutzbar (wie Homepage). |
| JSON | `api/rounds/{round}/fetchData` | Bereits Resource-JSON; `Rounds/Show` kann das nutzen. |
| DataTables JSON | `rounds/ajax/{group}/{player?}` | HTML in Spalten (`players`-Column); Refactor wenn Archiv-UI Vue-native wird. |
| Nur Redirect | `rounds/current`, `liste/{group}/beitreten`, `verlassen`, `schließen/{close}`, `listen/calculate`, `liste/calculate/*`, `players/calculate*` | Kein `Inertia::render`; ggf. später GET→POST für Mutations. |
| Form POST/PATCH/DELETE | `login` POST, `logout`, `register` POST, `password/*`, `rounds` store/update/destroy, `comments`, `api/*` … | Antwort meist Redirect oder JSON; an Inertia `router.post` / normale Laravel-Responses anbinden. |
| Sonstiges | `auth/redirect/{provider}`, `callback/{provider}` | OAuth-Flow; kein eigenes Inertia-Layout nötig. |
| Framework | `_debugbar/*`, `_ignition/*`, `telescope/*`, `broadcasting/auth` | Nicht Teil der App-Migration. |

### Empfohlene Migrations-Reihenfolge (Vorschlag)

1. **Gruppen-Liste + -Erstellung** (`groups.index`, `groups.create`) — **erledigt**; Create ist ein schlankes Namensformular (Inertia `useForm`), nicht die früher in der Matrix angenommene Spieler-Mehrfachauswahl.
2. **Runden-Archiv** (`rounds.index`) — **erledigt**: Inertia-Page + Vue-Tabelle (Nuxt UI), serverseitige Sortierung; DataTables-JSON nur noch für Legacy-Archiv-Tabs (`rounds.archiveTable`).
3. **Nutzer bearbeiten** (`users.edit`) — isoliert, gute Übung für Multi-Form-Pages.
4. **Statische Seiten** (Datenschutz, Impressum, Regeln) — schnelle Gewinne, wenig Logik.
5. **Auth-Bundle** (Login, Register, Passwort, Quick Register, Socialite-Attach) — gemeinsam planen wegen Layout/Guest-Wrapper, Redirects, Validierung.
6. **Profil- und Gruppen-Detail** (`Players/Show`, `Groups/Show`) — hohe Komplexität; Gruppen-Detail vor oder nach Profil je nach Abhängigkeit der wiederverwendbaren Komponenten (Badges, Chart-Blöcke).
7. **Runden-Detail** (`rounds.show`) — zuletzt oder in dedizierter Teilprojekt-Phase wegen Legacy-Vue und Live-Runde.
8. **Admin/Test** — nach Bedarf.

### Risiko-Übersicht (querschnittlich)

| Risiko | Massnahme |
|--------|-----------|
| Matrix-Zeile / Kommentar trifft die echte Blade nicht | Vor Codieren Blade + Template prüfen; Phantom-Features nicht einbauen; Backlog-Zeile korrigieren. |
| Doppeltes Vue (Legacy + Inertia) auf einer URL | Pro Route nur ein Render-Pfad; `rounds.show` bewusst entflechten. |
| Unbenannte Routen (Ziggy) | `profil/{player}` und fehlende `name` bei Charts nachziehen, wo das Frontend Links braucht. |
| DataTables mit HTML-Strings | Schrittweise durch Vue-Tabelle + Links ersetzen; bis dahin ggf. Legacy-Fragmente dokumentieren. |
| Auth-Traits erwarten Blade | `showLoginForm` / `showRegistrationForm` überschreiben, Tests/Redirects prüfen. |
| `Group`-JSON / Resources | Mit `Http/Resources` und Typen konsolidieren (siehe Tech-Debt-Dokument). |

### Phase D — Backlog-Dokumentation

- [x] Routen-Matrix und Migrations-Reihenfolge in diesem Dokument (Arbeitsreferenz fuer Agent/Team).
