# Dateien aus dem Home Repo

Regeln, Settings, Hooks und Skripte, die Pauls Repos teilen, liegen im privaten Repo `pauldiepold/home`. Hier im Repo sind es Kopien. Welche Dateien das sind, listet `.claude/home-sync.json`; die CI prüft, dass sie unverändert sind.

- Diese Dateien hier nicht ändern. Der nächste Sync überschreibt die Änderung, und die CI schlägt vorher fehl.
- Soll sich eine davon ändern, ein Issue in `pauldiepold/home` anlegen (`gh issue create -R pauldiepold/home`) und Paul darauf hinweisen. Lokal geht es auch direkt im Home Repo (`../home`), danach verteilt `node scripts/sync.mjs` die Änderung.
- Projektspezifische Werte für die geteilten Skripte stehen in `.claude/projekt.json`; die gehört dem Repo.
