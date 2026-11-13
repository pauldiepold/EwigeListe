# Playbook: Ein altes Laravel-Projekt mit Claude Code modernisieren

Leitfaden für ein Repo, in dem noch nie mit KI gearbeitet wurde. Er stammt aus der Migration der EwigeListe von Blade zu Inertia (Oktober/November 2026, Retro über PR #109). Das neue Repo liegt neben diesem, also ist `../EwigeListe` die **Referenz**: Was dort schon steht, wird kopiert und angepasst, nicht neu erfunden. Die Pfade unten sind relativ zu `../EwigeListe`.

Kandidat ist `../memorial`: Laravel 11, Models direkt in `app/`, rund 75 Blade-Views, 29 Vue-Komponenten, 2 Tests, keine CI, kein `docs/`. Das entspricht etwa dem Startpunkt der EwigeListe.

## So wird dieses Dokument benutzt

1. Im neuen Repo bekommt `CLAUDE.md` eine Zeile, die hierher zeigt (Phase 1).
2. Die erste Session mit Paul ist die **Planungsrunde** (Phase 2). Phase 0 liefert ihr den Bestand, Phase 1 läuft danach in der Reihenfolge, die die Planungsrunde festlegt.
3. Ab Phase 3 ist `docs/migration.md` im neuen Repo das Leitdokument, dieses Playbook nur noch Nachschlagewerk.

## Phase 0: Bestandsaufnahme (nur lesen, eine Session)

Ziel ist eine belastbare Grundlage für die Planungsrunde, noch ohne Änderungen am Code.

1. **Stack:** PHP- und Laravel-Version, direkte Composer- und npm-Abhängigkeiten (`composer show --direct`, `package.json`). Jede Abhängigkeit bekommt eine Einordnung: behalten, ersetzen oder entfernen.
2. **Oberfläche:** Alle Routen (`php artisan route:list --except-vendor`), gruppiert nach Seiten. Je Seite: Blade-View, eingebettete Vue-Komponenten und Formulare/Mutationen.
3. **Domäne:** Models, Beziehungen und Kernlogik. Wo steckt die Logik, die über Jahre gewachsen ist und keine Tests hat? Das sind die Kandidaten für **Charakterisierungstests** (siehe Fallen).
4. **Betrieb:** Hosting, Deploy-Weg, Cron/Queue, Mails, externe Dienste, Zugriff auf einen Prod-Dump. Vorlagen: `docs/betrieb.md`, `db-sync.sh`.
5. **Tests und Prüfungen:** Was läuft heute, was ist rot? Gibt es Lint- oder Format-Werkzeuge?

**Fertig, wenn:** Jede Route einer Seitengruppe zugeordnet ist, jede Abhängigkeit eingeordnet ist und die offenen Fragen für Paul als Liste vorliegen. Das Ergebnis kommt als Abschnitt „Ausgangslage“ in den Entwurf von `docs/migration.md`.

## Phase 1: Agenten-Umgebung, vor dem ersten Feature

Die meisten Reibungsverluste der EwigeListe kamen aus der Umgebung, nicht aus dem Code. Deshalb steht sie komplett, bevor Subagenten loslegen. Jede Zeile ist ein kleiner PR oder Teil eines gemeinsamen Setup-PRs.

| Baustein | Vorlage | Anpassen |
|---|---|---|
| Schlanke `CLAUDE.md`: Stack, Prüfungen, Arbeitsweise, Verweise | `CLAUDE.md` | Projektname, Domäne, Deploy. Nur Verweise und Regeln, die das Verhalten ändern. |
| Laravel Boost mit gekürzten Guidelines | `config/boost.php` (Abschnitt „Schlanke Guidelines“), `boost.json` | Ausschlüsse an den Stack anpassen, `AGENTS.md` per `boost:update` erzeugen, nie von Hand |
| Format-Hook nach jedem Edit, auch in Worktrees | `.claude/settings.json`, `.claude/hooks/post-edit.mjs` | Pfade, falls das Frontend woanders liegt |
| Pre-Commit-Prüfung (Prettier, Pint) | `.githooks/pre-commit`, `prepare` in `package.json` | Pfade |
| `.env` in Worktrees | `.worktreeinclude` | – |
| CI auf PRs nach `master` | `.github/workflows/ci.yml` | Schritte, die es noch nicht gibt, weglassen und später ergänzen |
| Deploy per OpenSSH, Staging manuell mit Prod-Dump | `.github/workflows/deploy-staging.yml`, `deploy-prod.yml`, `scripts/ci-deploy.sh` | Secrets, Pfade. Nicht `appleboy/ssh-action` (lud in der Retro wiederholt mit 504 nach). |
| Lokale DB aus dem Prod-Dump | `db-sync.sh` (gleich wie in praxis-app) | Nur `.env`-Schlüssel |
| Agenten-Doku | `docs/agents/*.md` | Repo-Name, Labels |
| Domänensprache | `GLOSSARY.md`, `docs/adr/` | Lazy über `/domain-modeling`, nicht vorab |
| Test-Grundlage | `tests/TestCase.php`, `database/factories/UserFactory.php` | Laufen zwei ID-Räume zufällig gleich (Benutzer/Spieler), in den Factories versetzen |
| Private Memory | `~/.claude/projects/<repo>/memory/` | Staging-URL, Prod-Datenmenge, Schema-Abweichungen von Prod |

**Reihenfolge innerhalb von Phase 1:**

1. Testsuite reparieren und CI einführen.
2. Pint als eigener PR mit großem Diff. Den Commit in `.git-blame-ignore-revs` eintragen, Vorlage: EwigeListe #69.
3. Hooks und Boost einrichten.
4. Deploy-Workflows anlegen.

Tooling-Entscheidungen (Inertia-Version, Wayfinder, Fortify, Vitest) fallen vor der ersten migrierten Seite, damit jede Seite nur einmal angefasst wird. In der EwigeListe sind das die ADRs 0001/0002 und #48.

**Fertig, wenn:** Ein trivialer PR lokal und in der CI grün ist, der Pre-Commit-Hook eine unformatierte Datei abweist und `scripts/ci-deploy.sh` einmal erfolgreich auf Staging deployt hat.

## Phase 2: Planungsrunde mit Paul

Skill `mattpocock-skills:grilling` mit dem Bestand aus Phase 0. Das Ergebnis ist ein `docs/migration.md` nach dem Muster der EwigeListe: Ziel, Leitplanken, Arbeitsweise, Roadmap mit Phasen und Issues, Go-Live-Kriterien und Entscheidungen.

Zu klären sind mindestens diese Punkte:

- **Zielbild:** Inertia + Vue 3 + TS + Nuxt UI wie in der EwigeListe, oder etwas anderes? Gleich bleiben spart am meisten, denn Skills (`inertia-pages`), Komponentenmuster und Tests lassen sich übernehmen.
- **Big Bang oder schrittweise:** In der EwigeListe lief alles auf dem Integrations-Branch `inertia-migration`, Staging wurde automatisch deployt, und Prod sah nie einen gemischten Stand. Das passt, wenn Prod solange stillstehen darf.
- **Reihenfolge der Seiten:** Zuerst Seiten, die wiederverwendbare Komponenten hervorbringen (EwigeListe: Liste vor Profil). Die riskanteste Kernlogik (EwigeListe: Live-Spiel) kommt zuletzt, mit eigener Spec und Tickets.
- **Design:** Funktional zuerst, Design gebündelt in eigener Phase mit Spikes (EwigeListe Phase 4).
- **Feature-Freeze:** Label für Features nach der Migration.
- **Texte:** Anrede, geschlechtsneutrale Formulierungen (EwigeListe: Du-Form, „Person“).

**Fertig, wenn:** `docs/migration.md` steht und je Phase ein Spec-Issue mit Tickets existiert. Die Tickets sind per Grilling geklärt und tragen das Label `ready-for-agent`.

## Phase 3: Umsetzung je Phase

Dieser Ablauf hat in PR #109 neun Tickets in einer Session getragen:

1. **Integrations-Branch** pro Spec (`mig/<nr>-kurzname`), Draft-PR mit Checkliste der Tickets.
2. **Wellen von Subagenten** (Sonnet, Effort high) in Worktrees, je Ticket ein Branch. Parallel nur, was sich nicht überschneidet. Grundlage ist `docs/agents/implementer.md`, ergänzt um eine Notizdatei für die Spec. Nach jedem Merge kommen Strukturnotizen dazu (neue Composables, Props-Formen), damit die nächste Welle sie nicht neu suchen muss.
3. **Der Hauptagent merged, prüft und deployt.** Nach jedem Merge laufen alle Prüfungen auf dem zusammengeführten Stand, dann `scripts/ci-deploy.sh` im Hintergrund. Paul gibt Feedback zu jedem Ticket auf Staging, solange der Kontext noch frisch ist.
4. **Review über zwei Achsen** (`mattpocock-skills:code-review`, Standards und Spec) über den ganzen Branch. Danach behebt **ein** Subagent alle Funde zusammen mit Pauls Backlog.
5. **Abnahme durch Paul** in echten Browsern, bei Echtzeit-Funktionen mit mehreren. Erst dann folgt der Squash-Merge, und die Issues werden geschlossen.

Der Hauptverlauf enthält nur Rückmeldungen, Merges und kurze Prüfausgaben (`| grep -E "Tests:|FAIL"`). Code liest und schreibt dort nur, wer gerade einen Konflikt auflöst oder eine kleine Korrektur von Paul umsetzt.

## Fallen aus der EwigeListe

- **Altverhalten ist Spec.** Agenten haben Standardwerte geändert, Design-Elemente erfunden und Antworten „aufgeräumt“. Das gehört in den Auftrag, und Abweichungen werden in der Rückmeldung benannt.
- **Alte Kernlogik hat unentdeckte Fehler.** Das Schmeißen im Live-Spiel hat jahrelang nie funktioniert. Vor dem Umbau Charakterisierungstests gegen den alten Stand schreiben. Dann ist klar, was Spec ist und was Fehler.
- **Prod ist nicht lokal.** Alte Migrationen ergeben auf Prod andere Spaltentypen als auf frischen DBs (`float(3,1)`). Datenmengen sind andere. Datenmigrationen deshalb immer auf Staging mit Prod-Dump laufen lassen (`sync_db=yes`).
- **Gleichlaufende IDs verstecken Verwechslungen.** Auf Prod galt zufällig `users.id == player_id`. Die Tests waren grün, gefunden hat es erst das Review.
- **Echtzeit und Seitenwechsel.** Zwei Fehler hat nur der Test mit vier Browsern gefunden: Presence nach dem Wechsel Runde → Live und ein Wettrennen beim Bereit-Check. Früh eine nur lokale Anmeldung als beliebige Person und getrennte Browser-Profile einplanen.
- **Herd-Workspaces** hängen am Upstream `origin/master`: Ein nacktes `git push` deployt Prod. Sofort `git branch --unset-upstream`.
- **Windows-Shell:** Kein Python. Rekursives Löschen blockiert der Auto-Modus. `!`-Befehle laufen in der Git-Bash, `cmd /c rmdir /s` geht dort ins Leere.

## Was neu zu entscheiden ist und nicht kopiert wird

Domänenbegriffe, Seitenstruktur, Design und die Projekt-Skills (`inertia-pages` erst schreiben, wenn die ersten zwei, drei Seiten stehen; vorher Konventionen aus `../EwigeListe/.claude/skills/inertia-pages` lesen und nur das Passende übernehmen).
