# Implementer im Worktree

Grundlage für Subagenten, die ein Ticket in `.claude/worktrees/<name>` umsetzen. Ticket, Branch und Notizen zum Stand nennt der Auftrag.

## Einrichten

`.env` kopiert Claude Code per `.worktreeinclude` mit. Danach `composer install`, `npm ci` und `npm run build` (erzeugt die Wayfinder-Routen).

## Arbeiten

- Dateien per Edit/Write ändern, dann formatiert der Hook sie gleich mit.
- Ein Befehl pro Bash-Aufruf, git nur im eigenen Worktree. Die Worktree-Sperre lehnt verkettete Befehle mit Heredocs, `sed` mit berechneten Argumenten und `git -C` ab.
- Bisheriges Verhalten (Standardwerte, Texte, Design) gilt als Spec. Abweichungen gehören in die Rückmeldung, statt sie selbst zu entscheiden.

## Abschluss

Alle Prüfungen aus `CLAUDE.md` sind grün, die Commits liegen auf dem Branch aus dem Auftrag. Die Rückmeldung nennt, was gebaut ist, Abweichungen vom Ticket und offene Fragen.

## Aufräumen (Hauptagent)

`git worktree remove --force <pfad>`, danach `git worktree prune`. Rekursives Löschen blockiert der Auto-Modus.
