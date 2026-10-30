# Ewige Liste

Web-App, mit der eine feste Doppelkopf-Runde von Freunden ihre Spiele erfasst, auswertet und online live gegeneinander spielt.

## Language

### Spielbetrieb

**Runde**:
Ein Doppelkopf-Abend mit fester Besetzung, in dem nacheinander mehrere Spiele gespielt werden. Eine Runde kann für mehrere Listen gleichzeitig zählen.
_Avoid_: Session, Partie, Abend

**Spiel**:
Ein einzelnes Doppelkopf-Spiel innerhalb einer Runde, mit Punkten für die beteiligten Spieler.
_Avoid_: Partie, Hand

### Personen

**Spieler**:
Eine Person (oder KI), die in Runden mitspielt und Punkte sammelt. Ein Spieler kann ohne Benutzer existieren.
_Avoid_: Mitspieler, Teilnehmer, User

**Benutzer**:
Das Login-Konto einer echten Person; gehört genau einem Spieler.
_Avoid_: Account, User, Spieler

**Schnellregistrierung**:
Ein angemeldeter Benutzer legt beim Erstellen einer Runde für eine andere Person einen Spieler samt Benutzer an. Die Person setzt dabei kein Passwort; sie kann später über „Passwort vergessen“ einsteigen.
_Avoid_: Quick-Register, Gastspieler

**Versteckter Spieler**:
Ein Spieler, der nicht mehr für neue Runden ausgewählt werden kann (z. B. ehemalige Mitspieler oder Testkonten). Wird nur direkt in der Datenbank gesetzt.
_Avoid_: Inaktiver Spieler, gelöschter Spieler

### Auswertung

**Liste**:
Eine benannte Sammlung von Spielern, deren gemeinsame Runden zusammen ausgewertet werden (Tabelle, Rekorde, Statistiken).
_Avoid_: Gruppe, Group, Tabelle

**Ewige Liste**:
Die besondere Liste der App, für die jede Runde automatisch zählt. Wird gezeigt, wenn keine andere Liste gewählt ist.
_Avoid_: Hauptliste, Default-Gruppe

**Standardliste**:
Eine Liste, die ein Spieler für sich markiert hat. Sie wird automatisch ausgewählt, sobald er einer neuen Runde hinzugefügt wird.
_Avoid_: Default-Liste, Lieblingsliste

**Geschlossene Liste**:
Eine Liste, der keine neuen Runden mehr hinzugefügt werden können. Ihre bisherigen Runden und Statistiken bleiben erhalten.
_Avoid_: Archivierte Liste, gesperrte Liste

**Profil**:
Ein Spieler in genau einer Liste, samt seiner Statistik in dieser Liste. Nicht das Benutzerkonto.
_Avoid_: Benutzerprofil, Account
