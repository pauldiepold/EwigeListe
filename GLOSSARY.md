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

### Auswertung

**Liste**:
Eine benannte Sammlung von Spielern, deren gemeinsame Runden zusammen ausgewertet werden (Tabelle, Rekorde, Statistiken).
_Avoid_: Gruppe, Group, Tabelle

**Ewige Liste**:
Die besondere Standard-Liste der App, die gezeigt wird, wenn keine andere Liste gewählt ist.
_Avoid_: Hauptliste, Default-Gruppe

**Profil**:
Ein Spieler in genau einer Liste, samt seiner Statistik in dieser Liste. Nicht das Benutzerkonto.
_Avoid_: Benutzerprofil, Account
