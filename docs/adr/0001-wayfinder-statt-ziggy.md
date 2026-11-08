# 0001 Wayfinder statt Ziggy für Routen im Frontend

**Status:** angenommen (2026-10-28, #48)

## Kontext

Das Frontend erzeugte URLs mit Ziggy (`useAppRoute()`, Routenliste als Inertia-Shared-Prop `ziggy`). Laravel bringt mit Wayfinder ein offizielles Paket, das aus Routen und Controllern typisierte TypeScript-Funktionen erzeugt. Die Starter-Kits nutzen Wayfinder statt Ziggy. Zum Zeitpunkt der Entscheidung waren 14 Seiten migriert und 38 `route()`-Aufrufe vorhanden.

## Entscheidung

Wir wechseln auf Wayfinder und bleiben damit im Laravel-Ökosystem.

- Generierung über `@laravel/vite-plugin-wayfinder` nach `resources/js/inertia/{routes,actions,wayfinder}` (Alias `@`). Diese Ordner sind generiert und stehen in `.gitignore`, `.prettierignore` und den ESLint-Ignores.
- Im Code: benannte Imports aus `@/routes/...`, URLs per `.url()`. Das HTTP-Verb bleibt am Aufruf (`form.post(login.url())`), weil sich GET- und POST-Routen oft eine URL teilen.
- Ziggy (`tightenco/ziggy`, `ziggy-js`, Shared Prop `ziggy`) ist entfernt.

## Konsequenzen

- Routennamen und Parameter werden vom Type-Check geprüft; ein umbenannter Routenname bricht den Build statt erst die Seite.
- Der Frontend-Build braucht PHP (`php artisan wayfinder:generate`). Die CI richtet PHP deshalb vor `npm run build` ein, ebenso das Staging-Deployment.
- Wayfinder ist noch v0.1.x; Updates vorher auf API-Änderungen prüfen.
