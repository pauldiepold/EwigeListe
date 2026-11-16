# Arbeitsweise

Der Name des Hauptbranches und der Deploy-Weg stehen im `CLAUDE.md` des Repos.

- Jede Änderung, auch reine Doku, ohne Rückfrage auf einem eigenen Branch vom Hauptbranch und per PR (Skill `pr`). Ein Push auf den Hauptbranch deployt Produktion, deshalb landet dort nur der gemergte PR.
- Tickets immer per Skill `implement` umsetzen, auch direkt nach einem Grilling; ohne den Skill nur, wenn Paul es ausdrücklich sagt. Abschluss: alle Prüfungen aus `CLAUDE.md` grün, PR nach dem Hauptbranch, erst nach Pauls Abnahme mergen (Squash) und das Issue schließen.
- „Deploy“ heißt Staging; Produktion nur, wenn Paul sie ausdrücklich nennt.
- Änderungen an Deploy-Workflows und Annahmen über den Prod-Betrieb (Queue, Cron, Server) nur nach Rückfrage bei Paul, auch wenn ein Review sie vorschlägt. Nichts über Prod behaupten, was das Repo nicht belegt.
- Kleine Aufräumarbeiten im Umfeld des Tickets (Review-Funde, Inkonsistenzen in angefassten Dateien) gleich mit erledigen; nur Größeres als eigenes Issue abgrenzen.
- Kleine Anfragen klein lösen. Übersteigt der „richtige“ Weg den Nutzen deutlich, die einfachste Variante wählen und in wenigen Sätzen begründen.
- Fehlt in der Shell ein Tool (`php`, `composer`, Netzwerk), Paul um die Ausführung bitten. Python gibt es nicht, Hilfsskripte mit `node`.

## Wissen festhalten

Es gibt keine private Memory: Cloud-Sessions sehen nur das Repo.

- Dauerhaftes zum Projekt (Betrieb, Datenmengen, Fallstricke, Entscheidungen) gehört ins Repo: `CLAUDE.md`, `docs/` oder ein ADR.
- Dauerhaftes zu Werkzeugen und Arbeitsweise gehört ins Home Repo, siehe `.claude/rules/home.md`.
