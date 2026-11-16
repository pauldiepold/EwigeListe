#!/bin/bash
# SessionStart-Hook: richtet eine Cloud-Session so ein, dass die Prüfungen aus CLAUDE.md laufen.
# Lokal (ohne CLAUDE_CODE_REMOTE=true) tut er nichts. Jeder Schritt wird übersprungen, wenn er schon erledigt ist.
# Die Ausgabe landet im Kontext der Session, deshalb meldet er knapp, was fehlt.
[ "${CLAUDE_CODE_REMOTE:-}" = "true" ] || exit 0
cd "${CLAUDE_PROJECT_DIR:-.}" || exit 0

SUDO=""; [ "$(id -u)" -eq 0 ] || SUDO="sudo"
php_ok() { php -r 'exit(version_compare(PHP_VERSION, "8.4.0", ">=") ? 0 : 1);' 2> /dev/null; }

if ! php_ok; then
  # Das Cloud-Image bringt PHP 8.3 mit, die Repos verlangen 8.4.
  {
    $SUDO add-apt-repository -y ppa:ondrej/php \
      && $SUDO apt-get update -qq \
      && $SUDO apt-get install -y -qq php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-sqlite3 php8.4-gd php8.4-intl php8.4-bcmath \
      && $SUDO update-alternatives --set php /usr/bin/php8.4
  } > /tmp/cloud-setup-php.log 2>&1
fi

if ! php_ok; then
  echo "cloud-setup: PHP 8.4 fehlt und ließ sich nicht installieren (Log: /tmp/cloud-setup-php.log)."
  echo "Vermutlich blockt das Netzwerk der Umgebung ppa.launchpadcontent.net. PHP-Prüfungen laufen so nicht; das Paul melden."
  exit 0
fi

[ -d vendor ] || composer install --no-interaction --no-progress --prefer-dist > /tmp/cloud-setup-composer.log 2>&1 \
  || echo "cloud-setup: composer install fehlgeschlagen (Log: /tmp/cloud-setup-composer.log)."

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
  php artisan key:generate --no-interaction > /dev/null 2>&1
fi

[ -d node_modules ] || npm ci > /tmp/cloud-setup-npm.log 2>&1 \
  || echo "cloud-setup: npm ci fehlgeschlagen (Log: /tmp/cloud-setup-npm.log)."

# Der Build erzeugt die Wayfinder-Routen, ohne die Type-Check und Tests im Frontend scheitern.
[ -d public/build ] || npm run build > /tmp/cloud-setup-build.log 2>&1 \
  || echo "cloud-setup: npm run build fehlgeschlagen (Log: /tmp/cloud-setup-build.log)."

echo "cloud-setup: fertig ($(php -r 'echo PHP_VERSION;'), node $(node -v))."
