# Umgebung der Laravel-Repos

## Format und Prüfungen

- Nach jedem Edit/Write formatiert `.claude/hooks/post-edit.mjs` (Prettier, `php -l`, Pint). Dateien deshalb per Edit/Write ändern, nicht per Shell.
- Vor jedem Commit prüft `.githooks/pre-commit` das Format, auch bei Änderungen per Shell; `npm install` aktiviert den Hook.
- Welche Prüfungen vor einem PR grün sein müssen, steht im `CLAUDE.md` des Repos. Die CI prüft dasselbe.

## Staging-Deploy

Staging deployt nur manuell: ohne Rückfrage, wenn `implement` ein größeres Feature mit offenem PR abschließt, sonst auf Pauls Wunsch. Kleine Änderungen nimmt Paul lokal ab.

`scripts/ci-deploy.sh [sha] [branch]` im Hintergrund starten: Es wartet auf die CI, deployt mit Prod-Dump und wartet aufs Ende. Danach Paul mit „deployed“ melden.

## Lokale Datenbank

`./db-sync.sh` (Git Bash) holt den neuesten Prod-Dump per FTP (Zugang über `FTP_*` in `.env`), legt die lokale Datenbank aus `.env` neu an, importiert ihn, ruft `migrate` auf und danach die Befehle aus `nachImport` in `.claude/projekt.json`. Es läuft nur bei `APP_ENV=local`. Ohne `mysql` im PATH importiert es per `docker exec` in den MySQL-Container auf `DB_PORT`; der gehört allen Laravel-Repos gemeinsam und wird im Home Repo unter `lokal/` verwaltet. Läuft er nicht, bricht das Skript vor dem Download mit Hinweis ab.

## Herd-Workspaces

- `herd workspace <name>` verknüpft den neuen Branch mit dem Hauptbranch auf `origin`. Ein nacktes `git push` würde dort landen und Produktion deployen. Im frischen Workspace deshalb sofort `git branch --unset-upstream` und beim ersten Push `git push -u origin <branch>`.
- `--database=clone` scheitert mit Fehler 1044, weil der MySQL-Benutzer keine Datenbanken anlegen darf. Der Workspace nutzt dann still die gemeinsame Datenbank: Schemaänderungen treffen die Hauptseite und andere Workspaces mit.

## Cloud-Sessions

`.claude/hooks/cloud-setup.sh` richtet die Session beim Start ein (PHP, `composer install`, `npm ci`, Build, `.env` mit SQLite). Lokal tut der Hook nichts. Herd, die MySQL-Datenbank und `db-sync.sh` gibt es in der Cloud nicht; Tests laufen dort wie in der CI gegen SQLite.

## Subagenten in Worktrees

Bekommen `docs/agents/implementer.md` als Grundlage.
