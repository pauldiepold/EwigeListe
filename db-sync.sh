#!/bin/bash
# Spielt den neuesten Prod-Dump (per FTP) in die lokale Datenbank aus `.env`.
# Übernommen aus praxis-app; läuft in Git Bash.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$SCRIPT_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
  echo "Fehler: .env nicht gefunden unter $ENV_FILE"
  exit 1
fi

# Wert aus .env: alles nach dem ersten `=`, ohne CR und umschließende Anführungszeichen.
env_value() {
  grep "^$1=" "$ENV_FILE" | head -1 | cut -d '=' -f2- | tr -d '\r' | sed -e 's/^"\(.*\)"$/\1/' -e "s/^'\(.*\)'$/\1/"
}

echo "=== .env lesen ==="
APP_ENV=$(env_value APP_ENV)
DB_HOST=$(env_value DB_HOST)
DB_PORT=$(env_value DB_PORT)
DB_DATABASE=$(env_value DB_DATABASE)
DB_USERNAME=$(env_value DB_USERNAME)
DB_PASSWORD=$(env_value DB_PASSWORD)
FTP_HOST=$(env_value FTP_HOST)
FTP_USER=$(env_value FTP_USER)
FTP_PASSWORD=$(env_value FTP_PASSWORD)
FTP_PATH=$(env_value FTP_PATH)
FTP_PATH="${FTP_PATH:-/}"
MYSQL_CONTAINER=$(env_value MYSQL_CONTAINER)
MYSQL_CONTAINER="${MYSQL_CONTAINER:-phpstormprojects-mysql-1}"

# Der Import überschreibt die Datenbank: nur lokal.
if [ "$APP_ENV" != "local" ]; then
  echo "Fehler: APP_ENV ist '$APP_ENV', nicht 'local'. Abbruch."
  exit 1
fi

if [ -z "$FTP_HOST" ] || [ -z "$FTP_USER" ] || [ -z "$FTP_PASSWORD" ]; then
  echo "Fehler: FTP_HOST, FTP_USER und FTP_PASSWORD in .env setzen."
  exit 1
fi

echo "DB:  $DB_DATABASE auf $DB_HOST:$DB_PORT (User: $DB_USERNAME)"
echo "FTP: $FTP_USER@$FTP_HOST$FTP_PATH"

echo ""
echo "=== Neuesten Dump suchen ==="
LATEST_DUMP=$(curl -s --list-only "ftp://$FTP_USER:$FTP_PASSWORD@$FTP_HOST$FTP_PATH" \
  | tr -d '\r' \
  | grep "\.sql\.gz$" \
  | sort \
  | tail -1 || true)

if [ -z "$LATEST_DUMP" ]; then
  echo "Fehler: Kein Dump gefunden unter $FTP_PATH"
  exit 1
fi

echo "Gefunden: $LATEST_DUMP"

DUMP_FILE="$(mktemp)"
trap 'rm -f "$DUMP_FILE"' EXIT

echo ""
echo "=== Dump herunterladen ==="
curl --ftp-pasv --fail --progress-bar "ftp://$FTP_USER:$FTP_PASSWORD@$FTP_HOST$FTP_PATH$LATEST_DUMP" -o "$DUMP_FILE"
echo "Download abgeschlossen: $(du -h "$DUMP_FILE" | cut -f1)"

# Lokaler Client, sonst der im MySQL-Container (Git Bash bringt keinen mit).
if command -v mysql > /dev/null; then
  MYSQL=(mysql --host="$DB_HOST" --port="$DB_PORT")
else
  echo "Kein mysql-Client gefunden, nutze Container $MYSQL_CONTAINER"
  MYSQL=(docker exec -i "$MYSQL_CONTAINER" mysql)
fi

echo ""
echo "=== Dump entpacken und importieren ==="
echo "Zieldatenbank: $DB_DATABASE"
gunzip < "$DUMP_FILE" | "${MYSQL[@]}" \
  --user="$DB_USERNAME" \
  --password="$DB_PASSWORD" \
  "$DB_DATABASE"
echo "Import abgeschlossen"

echo ""
echo "=== Fertig ==="
