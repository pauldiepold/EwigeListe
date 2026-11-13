#!/usr/bin/env bash
#
# db-sync.sh – spielt den neuesten Prod-Dump in die lokale Datenbank.
#
# Aufruf:  ./db-sync.sh        (Git Bash unter Windows, WSL, macOS, Linux)
#
# Ablauf:
#   1. Liest Zugangsdaten aus `.env` neben dem Skript; bricht ab, wenn APP_ENV nicht `local` ist.
#   2. Sucht einen MySQL-Client: `mysql` im PATH, sonst `docker exec` in den Container,
#      der DB_PORT veröffentlicht (z. B. `phpstormprojects-mysql-1`). Ist keiner erreichbar,
#      Abbruch mit Hinweis, noch vor dem Download.
#   3. Holt per FTP die alphabetisch letzte `*.sql.gz` aus FTP_PATH (Dateinamen mit Datum,
#      also die neueste) in eine temporäre Datei außerhalb des Repos; sie wird immer gelöscht.
#   4. Legt die Datenbank neu an (DROP + CREATE), damit keine Tabellen aus Branch-Migrationen
#      übrig bleiben, und importiert den Dump.
#   5. `php artisan migrate --force` zieht Migrationen des aktuellen Branches nach,
#      danach laufen die projektspezifischen Befehle aus NACH_IMPORT.
#
# `.env`-Schlüssel:
#   APP_ENV                 muss `local` sein
#   DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#   FTP_HOST, FTP_USER, FTP_PASSWORD
#   FTP_PATH                optional, Verzeichnis der Dumps, Standard `/`
#   MYSQL_CONTAINER         optional, Containername statt automatischer Suche über DB_PORT
#
# In ein anderes Projekt übernehmen: Datei kopieren, NACH_IMPORT anpassen, `FTP_*` in `.env`
# eintragen. Alles andere ist generisch und soll in allen Kopien gleich bleiben.

set -euo pipefail

# Projektspezifisch: Befehle nach Import und Migration (leer lassen, wenn nichts nötig ist).
NACH_IMPORT=()

# Herd unter Windows liefert nur php.bat (in Git Bash sonst per Alias, der im Skript nicht gilt).
command -v php > /dev/null || php() { php.bat "$@"; }

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$SCRIPT_DIR/.env"
cd "$SCRIPT_DIR"

fehler() {
  echo "Fehler: $*" >&2
  exit 1
}

[ -f "$ENV_FILE" ] || fehler ".env nicht gefunden unter $ENV_FILE"

# Wert aus .env: alles nach dem ersten `=`, ohne CR und umschließende Anführungszeichen.
env_wert() {
  { grep "^$1=" "$ENV_FILE" || true; } | head -1 | cut -d '=' -f2- | tr -d '\r' \
    | sed -e 's/^"\(.*\)"$/\1/' -e "s/^'\(.*\)'$/\1/"
}

APP_ENV=$(env_wert APP_ENV)
DB_HOST=$(env_wert DB_HOST)
DB_PORT=$(env_wert DB_PORT)
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE=$(env_wert DB_DATABASE)
DB_USERNAME=$(env_wert DB_USERNAME)
DB_PASSWORD=$(env_wert DB_PASSWORD)
FTP_HOST=$(env_wert FTP_HOST)
FTP_USER=$(env_wert FTP_USER)
FTP_PASSWORD=$(env_wert FTP_PASSWORD)
FTP_PATH=$(env_wert FTP_PATH)
FTP_PATH="${FTP_PATH:-/}"
[[ "$FTP_PATH" == */ ]] || FTP_PATH="$FTP_PATH/"
MYSQL_CONTAINER=$(env_wert MYSQL_CONTAINER)

# Der Import überschreibt die Datenbank: nur lokal.
[ "$APP_ENV" = "local" ] || fehler "APP_ENV ist '$APP_ENV', nicht 'local'."
[ -n "$DB_DATABASE" ] && [ -n "$DB_USERNAME" ] || fehler "DB_DATABASE und DB_USERNAME in .env setzen."
[ -n "$FTP_HOST" ] && [ -n "$FTP_USER" ] && [ -n "$FTP_PASSWORD" ] \
  || fehler "FTP_HOST, FTP_USER und FTP_PASSWORD in .env setzen."

echo "=== MySQL suchen ==="
if command -v mysql > /dev/null; then
  echo "Lokaler Client, $DB_HOST:$DB_PORT"
  mysql_ausfuehren() {
    MYSQL_PWD="$DB_PASSWORD" mysql --host="$DB_HOST" --port="$DB_PORT" --user="$DB_USERNAME" "$@"
  }
else
  command -v docker > /dev/null \
    || fehler "Weder mysql noch docker gefunden. Docker Desktop installieren oder einen mysql-Client in den PATH legen."
  if [ -z "$MYSQL_CONTAINER" ]; then
    MYSQL_CONTAINER=$(docker ps --filter "publish=$DB_PORT" --format '{{.Names}}' 2> /dev/null | head -1 || true)
  fi
  [ -n "$MYSQL_CONTAINER" ] \
    || fehler "Kein laufender Container auf Port $DB_PORT. Docker Desktop starten, dann im Compose-Ordner (z. B. PhpstormProjects) 'docker compose up -d'."
  echo "Container $MYSQL_CONTAINER"
  mysql_ausfuehren() {
    docker exec -i -e MYSQL_PWD="$DB_PASSWORD" "$MYSQL_CONTAINER" mysql --user="$DB_USERNAME" "$@"
  }
fi
mysql_ausfuehren -e 'SELECT 1' > /dev/null \
  || fehler "MySQL nimmt keine Verbindung an (läuft der Server, stimmen DB_USERNAME/DB_PASSWORD?)."

echo ""
echo "=== Neuesten Dump suchen ==="
echo "FTP: $FTP_USER@$FTP_HOST$FTP_PATH"
LISTE=$(curl --silent --show-error --fail --list-only --user "$FTP_USER:$FTP_PASSWORD" "ftp://$FTP_HOST$FTP_PATH") \
  || fehler "FTP-Verzeichnis nicht lesbar."
DUMP_NAME=$(echo "$LISTE" | tr -d '\r' | { grep '\.sql\.gz$' || true; } | sort | tail -1)
[ -n "$DUMP_NAME" ] || fehler "Kein *.sql.gz unter $FTP_PATH gefunden."
echo "Gefunden: $DUMP_NAME"

DUMP_FILE="$(mktemp)"
trap 'rm -f "$DUMP_FILE"' EXIT

echo ""
echo "=== Dump herunterladen ==="
curl --fail --progress-bar --user "$FTP_USER:$FTP_PASSWORD" "ftp://$FTP_HOST$FTP_PATH$DUMP_NAME" -o "$DUMP_FILE"
gzip --test "$DUMP_FILE" || fehler "Download ist kein gültiges gzip."
echo "Heruntergeladen: $(du -h "$DUMP_FILE" | cut -f1)"

echo ""
echo "=== Datenbank $DB_DATABASE neu anlegen und importieren ==="
mysql_ausfuehren -e "DROP DATABASE IF EXISTS \`$DB_DATABASE\`; CREATE DATABASE \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
gunzip < "$DUMP_FILE" | mysql_ausfuehren "$DB_DATABASE"
echo "Import abgeschlossen"

echo ""
echo "=== Migrationen des Branches ==="
php artisan migrate --force

if [ ${#NACH_IMPORT[@]} -gt 0 ]; then
  echo ""
  echo "=== ${NACH_IMPORT[*]} ==="
  "${NACH_IMPORT[@]}"
fi

echo ""
echo "=== Fertig: $DUMP_NAME ist lokal eingespielt ==="
