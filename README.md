# EwigeListe

Web-App zum Offline- und Online-Doppelkopf spielen.

## Installationsanleitung
Benötigt: WSL2 + Docker
1. Repository klonen
2. [Composer-Dependencies installieren](https://laravel.com/docs/8.x/sail#executing-composer-commands)
3. [Sail Bash Alias einrichten](https://laravel.com/docs/8.x/sail#configuring-a-bash-alias)
4. ```.env``` Datei einrichten
5. Mit ```sail up``` Docker Container starten (bzw. beim ersten Ausführen Images erstellen)
6. Mit ```sail php artisan migrate --seed``` Datenbank migrieren und seeden
7. App ist jetzt unter [http://localhost](http://localhost) erreichbar

## Hinweise und wichtige Befehle
* Kompilieren vom Javascript mit ```sail npm run prod``` bzw. ```sail npm run watch-poll```
* Passworter der geseedeten Benutzer bzw. KIs sind hier zu ändern: ```/database/seeds/PlayerSeeder.php```
* App-Key von [Pusher](https://pusher.com) (externer Service zur Kommunikation mit den Clients) muss im ```.env``` entsprechend der Umgebung gesetzt werden (Lokal, Staging, Prod-Webseite). Dazu ```MIX_PUSHER_APP_KEY``` in ```.env``` entsprechend setzen, anschließend Javascript neu kompilieren.
* Datenbank neu migrieren und seeden: ```sail php artisan migrate:fresh --seed```
