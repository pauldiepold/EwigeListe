# 0002 Fortify statt laravel/ui für die Authentifizierung

**Status:** angenommen (2026-11-07, #46, #56)

Login, Registrierung und Passwort-Reset liefen über die Traits aus `laravel/ui` (`AuthenticatesUsers`, `RegistersUsers`, …). Die Controller überschrieben davon nur die View-Methoden für Inertia. Ursprünglich sollten sie durch eigene schlanke Controller ersetzt werden, weil Fortify „zu viel“ sei. Wir nehmen stattdessen `laravel/fortify`, aufgebaut wie im Vue-Starter-Kit (`FortifyServiceProvider`, `app/Actions/Fortify/*`, `Fortify::*View` mit `Inertia::render()`). Die Starter-Kits seit Laravel 12 nutzen Fortify, und die Features werden einzeln eingeschaltet. Dass Fortify mehr kann, als wir brauchen, kostet uns also nichts, während eigene Controller wieder Code wären, den nur wir pflegen.

## Konsequenzen

- Socialite gehört nicht zu Fortify und bleibt ein eigener Controller. Die Verknüpfung einer Socialite-Identität läuft über die Session und einen Listener auf das Framework-Event `Login` (Fortify loggt auch nach der Registrierung ein), nicht über Formularfelder (#56). Damit hängt sie nicht an Fortify.
- Das Captcha prüft die Action `CreateNewUser`.
- Abweichung vom Kit: Nach dem Reset erscheint die Meldung als Toast (eigene Response-Klasse) statt als `status`-Prop, wie überall sonst in der App. Nach der Link-Anforderung ersetzt eine Bestätigung auf `Auth/ForgotPassword` das Formular; ein Toast wäre für den Hinweis aufs Postfach zu flüchtig.
- Passwort ändern bleibt wie im Kit ein eigener Controller (`users.updatePassword`), weil der Admin dort auch fremde Passwörter ohne das alte ändert.
