# Betrieb: Queue und Scheduler auf Prod und Staging

Wie Prod und Staging Hintergrund-Jobs und geplante Befehle abarbeiten. Das Repo ist öffentlich: Pfade, Konto und Domains stehen hier nur als Platzhalter (`<PROD_PATH>`, `<STAGING_PATH>`, `<SKRIPT_DOMAIN>`), wie in den Deploy-Workflows als Secrets.

## Überblick

- Die Queue läuft auf Prod und Staging über den Treiber `database` (Tabelle `jobs`, Fehlschläge in `failed_jobs`).
- Auf dem Server läuft kein dauerhafter Prozess (kein Supervisor, kein `queue:work` im Hintergrund). Stattdessen ruft ein Cronjob per HTTP ein PHP-Skript auf. Das Skript startet einmal `queue:work` und danach `schedule:run` und beendet sich wieder.
- **Prod:** Cron jede Minute. **Staging:** gleiches Skript daneben mit dem Staging-Pfad, Cron alle 5 Minuten.
- Lokal läuft stattdessen `php artisan queue:listen --tries=1` im eigenen Terminal-Tab (siehe `CLAUDE.md`), Tests nutzen `sync`.

## Was über die Queue läuft

Nur die Statistik:

| Job | Ausgelöst von | Inhalt |
|---|---|---|
| `App\Jobs\UpdateProfile` | eingetragenes, gelöschtes oder live beendetes Spiel und geänderte Listen einer Runde (`Profile::updateManyStats`), `stats:recalculate` | ein Profil samt Abzeichen; lädt alle Spiele des Profils, braucht auf Prod rund 1 s und spürbar Speicher |
| `App\Jobs\UpdateGroup` | dieselben Anlässe (`Group::updateManyStats`), `stats:recalculate` (ein Job pro Liste und Monat) | Listenstatistik eines Monats |

Das Flag `queued` an Profil und Liste verhindert doppelt eingestellte Jobs; der Job setzt es vor der Berechnung zurück.

**Nicht** über die Queue laufen die Echtzeit-Events: `RoundUpdated`, `LiveGameDataBroadcasted` und `LiveGameDataBroadcastedInaktiv` sind `ShouldBroadcastNow` und gehen direkt an Pusher. Rundenseite und Live-Spiel hängen also nicht am Cron. Ein wartender Job heißt nur: Die Statistik ist noch nicht aktuell.

## Das Skript

Liegt außerhalb der App in einem eigenen Verzeichnis unter einer eigenen Domain, per `.htaccess` geschützt. Prod-Fassung:

```php
<?php
exec("/usr/bin/php84 <PROD_PATH>/artisan queue:work --stop-when-empty --memory=384 --max-time=50 2>&1", $out, $result);
exec("/usr/bin/php84 <PROD_PATH>/artisan schedule:run 2>&1", $out2, $result2);

echo "Ewige-Liste Queue:<br>Returncode: " .$result ."<br>";
echo "Ausgabe des Scripts: " ."<br>";
echo "<pre>"; print_r($out);
echo "</pre>";

echo "<hr>";

echo "Ewige-Liste Schedule:<br>Returncode: " .$result2 ."<br>";
echo "Ausgabe des Scripts: " ."<br>";
echo "<pre>"; print_r($out2);
echo "</pre>";
```

Die Staging-Fassung ist identisch, nur mit `<STAGING_PATH>`. Der Cron ruft `https://<SKRIPT_DOMAIN>/…` mit den `.htaccess`-Zugangsdaten auf.

### Die Optionen von `queue:work`

- `--stop-when-empty`: Der Worker beendet sich, sobald die Queue leer ist. Im Alltag ist sie das sofort, der Aufruf dauert dann nur Sekundenbruchteile.
- `--memory=384`: Der Worker prüft **nach** jedem Job seinen Speicher und beendet sich sauber (Exit-Code 12), wenn er mehr als 384 MB belegt. Ohne die Option gilt 128 MB; dann stoppt er bei einer Neuberechnung schon nach ein, zwei `UpdateProfile`-Jobs. Die harte Grenze ist das `memory_limit` der PHP-CLI (auf Prod 512 MB, `php84 -r 'echo ini_get("memory_limit");'`). Wird sie mitten im Job erreicht, bricht der Job mit „Allowed memory size exhausted“ ab. Deshalb liegt `--memory` mit gut 128 MB Abstand darunter, damit ein weiterer Job noch hineinpasst. Ein `--memory` über dem `memory_limit` würde nie greifen.
- `--max-time=50`: Der Worker beendet sich nach spätestens 50 Sekunden (nach dem laufenden Job). Damit
  - überschneiden sich die minütlichen Aufrufe praktisch nie,
  - kommt `schedule:run` noch in derselben Minute dran (siehe unten),
  - bleibt die HTTP-Anfrage unter den Timeouts von Webserver und Cron-Dienst, die sonst den Worker mitten im Job beenden könnten.

Überschneiden sich trotzdem zwei Worker, ist das unkritisch: Der `database`-Treiber reserviert jeden Job mit einer Sperre, kein Job läuft doppelt. Doppelt laufen würde ein Job nur, wenn er länger als `retry_after` dauert (90 s, `config/queue.php`); die Statistik-Jobs liegen weit darunter.

### `schedule:run`

Startet die Befehle aus `routes/console.php`, die in genau dieser Minute fällig sind:

| Befehl | Zeitpunkt | Hinweis |
|---|---|---|
| `stats:recalculate` | am 1. jedes Monats, 04:10 | stellt alle Profile und Listenmonate neu in die Queue, damit „in diesem Monat“ nach dem Monatswechsel stimmt |
| `telescope:prune` | täglich 03:55, nur `local` | Telescope ist auf Prod und Staging nicht registriert |

`schedule:run` holt verpasste Minuten nicht nach. Deshalb muss der Worker davor rechtzeitig enden (`--max-time=50`). Auf Staging läuft das Skript nur alle 5 Minuten; 04:10 trifft trotzdem, solange der Cron auf vollen Fünf-Minuten-Schritten liegt.

## Dauer einer Neuberechnung

`stats:recalculate` stellt einige Hundert Jobs ein. Am Stück (`--memory=1024`, ohne `--max-time`) dauerte das beim Go-Live auf Prod gut drei Minuten. Über den Cron verteilt sich das auf etwa vier bis fünf Aufrufe à 50 Sekunden, also auf Prod rund 5 Minuten, auf Staging (alle 5 Minuten) rund 20 bis 25 Minuten. Spiele, die währenddessen eingetragen werden, stehen mit ihrer Statistik hinten an; Rundenseite und Live-Spiel sind davon nicht betroffen.

Schneller geht es von Hand per SSH:

```
php artisan queue:work --stop-when-empty --memory=384
```

## Wartungsmodus und Deploy

- Im Wartungsmodus (`php artisan down`, auch während jedes Deploys) arbeitet `queue:work` ohne `--force` keine Jobs ab, und `schedule:run` startet keine Befehle. Die Jobs bleiben in `jobs` liegen und laufen nach `artisan up` mit dem nächsten Cron-Aufruf.
- Die Queue im Wartungsmodus leeren (z. B. vor einem Deploy, der Job-Klassen umbenennt): `php artisan queue:work --stop-when-empty --memory=384 --force`, danach prüfen, ob `jobs` leer ist.
- Prod deployt bei jedem Push auf `master`, Staging nur manuell (Workflow „Deploy Staging“ auf einem Feature-Branch, meist mit `sync_db=yes`).
- Der Staging-Deploy startet nach `sync_db` selbst `stats:recalculate` (`.github/workflows/deploy-staging.yml`), der Prod-Deploy nicht. Über den 5-Minuten-Cron dauert die Neuberechnung auf Staging rund 20 bis 25 Minuten; bis dahin zeigt Staging teils noch die Statistik aus dem Dump.
- Beide Deploys installieren mit `composer install --no-dev`: Debugbar, Telescope, Boost, Pint, PHPUnit und Faker fehlen auf dem Server. Factories und Seeder laufen dort deshalb nicht; `tinker` ist eine normale Abhängigkeit und bleibt verfügbar.

## Fehlersuche

- Ausgabe und Returncode des letzten Laufs: das Skript im Browser aufrufen. `0` heißt Queue leer, `12` heißt Speichergrenze erreicht (der nächste Aufruf macht weiter).
- Fehlgeschlagene Jobs: `php artisan queue:failed`, erneut starten mit `php artisan queue:retry all`.
- Wartende Jobs: `SELECT COUNT(*) FROM jobs;`
- Scheitert ein Job an „Allowed memory size exhausted“, reicht das `memory_limit` für ein einzelnes Profil nicht. Dann den Speicherverbrauch von `UpdateProfile` (`Profile::calculate`) ansehen, statt das Limit blind zu erhöhen.
