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

- [ ] Kleinen Spike auf 1-2 realen UI-Ausschnitten durchfuehren (z. B. Formular + Tabelle/Overlay).
- [ ] Bewertung nach festen Kriterien: Integrationsaufwand, Entwicklungsgeschwindigkeit, Wartbarkeit.
- [ ] Entscheidung dokumentieren (Nuxt UI oder kein neues Framework) und als Standard festhalten.

Ergebnis: Technologiewahl ist validiert, bevor weitere Seiten migriert werden.

### Phase D - Go/No-Go fuer eigentliche Seitenmigration

- [ ] Pilot-Review: Muster, Folder-Struktur, Tooling-Scope und UI-Entscheidung final bestaetigen.
- [ ] Erst danach Start der eigentlichen Route-fuer-Route-Migration.
- [ ] Migration weiterhin in kleinen, verifizierbaren Schritten mit Status `todo`, `in_progress`, `done`.

Ergebnis: Skalierbarer, risikoarmer Startpunkt fuer die komplette Migration.

## Definition of Done fuer die Pilotphase

- Homepage ist als Inertia-Page produktiv nutzbar.
- Build ist stabil; Lint und Type-Check laufen fuer den neuen Inertia-Bereich sauber durch.
- Legacy-Bereich bleibt klar getrennt und wird nicht versehentlich in neue Lint-Regeln gezogen.
- UI-Framework-Entscheidung ist getroffen und dokumentiert.

## Offene Fragen, die vor Umsetzung immer aktiv geklaert werden

- Welche Homepage-Daten sind zwingend initial serverseitig zu liefern und welche duerfen spaeter lazy geladen werden?
- Welche Shared-Data-Felder sind global erforderlich (auth, locale, flash, feature flags)?
- Welche Legacy-Bereiche muessen waehrend der Pilotphase garantiert unveraendert bleiben?
