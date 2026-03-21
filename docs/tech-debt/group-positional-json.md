# Tech-Debt: Positionsbasiertes JSON-Format in `Group::records` und `Group::stats`

**Priorität:** niedrig – kein akuter Handlungsbedarf, aber bei nächster Berührung der Berechnungslogik sinnvoll anzugehen  
**Bereich:** `app/Group.php`, Spalten `records` und `stats` in der DB  
**Entdeckt bei:** Inertia-Migration der Homepage (Phase B)

---

## Was ist das Problem?

`Group::calculate()` berechnet Rekorde und Statistiken und speichert sie als JSON-Blob in der Datenbank – aber in einem **anonymen, positionsbasierten Format**:

```php
// getHighscoreCollection() baut jede Zeile so:
$col->push($column->get(3));        // Position 0 → Label
$col->push($value . $column->get(6)); // Position 1 → Wert
$col->push($this->getPlayerLink($profiles)); // Position 2 → HTML-Link

// calcStats() macht dasselbe:
$colRow->push('Spiele insgesamt:'); // Position 0
$colRow->push($gamesAll);           // Position 1
```

Was in der DB landet sieht ungefähr so aus:

```json
[
  ["Meiste Spiele:", "142", "<a href='/profil/3/1'>Müller</a>"],
  ["Höchste Punktzahl:", "240", "<a href='/profil/5/1'>Schmidt</a>"]
]
```

Kein Feld hat einen Namen. Wer das liest, muss wissen, dass Index 0 das Label ist, Index 1 der Wert, Index 2 der Spieler-Link.

---

## Was wird dadurch kompliziert?

**1. Frontend-Integration erfordert einen Übersetzungsschritt**

Blade konnte `$row->shift()` dreimal hintereinander aufrufen – destruktiv, aber funktional. Vue kennt kein `shift()`. Deshalb braucht die `GroupHomeResource` extra Code, der die Positionen in benannte Felder umwandelt:

```php
// GroupHomeResource.php – dieser Code existiert nur wegen des schlechten Datenformats
collect($row)->get(0, '') // → labelHtml
collect($row)->get(1, '') // → value
collect($row)->get(2, '') // → detailHtml
```

Für jede weitere Seite oder Komponente, die `records`/`stats` anzeigt, braucht es denselben Übersetzungsschritt erneut.

**2. HTML ist in den Daten eingebettet**

`getPlayerLink()` baut einen `<a>`-Tag als String und speichert ihn direkt im JSON. Das bedeutet:
- Vue muss `v-html` verwenden, was XSS-Risiken mit sich bringt
- Links können nicht als Inertia-`<Link>`-Komponente gerendert werden (kein SPA-Routing)
- Wenn sich die URL-Struktur ändert, sind alle gespeicherten Links falsch

**3. Schlechte Lesbarkeit**

`getHighscoreCollection()` baut seine Konfiguration aus einer 7-elementigen anonymen Collection:
```php
collect(['mostGames', 'games', 'max', 'Meiste Spiele:', 'games', 0, ''])
//         [0]          [1]    [2]         [3]            [4]   [5] [6]
```
Index 0 ist der DB-Spaltenname, Index 3 das anzuzeigende Label, Index 4 die Zähler-Spalte usw. Ohne Kommentar nicht lesbar.

---

## Wie sähe es besser aus?

Statt positionsbasierter Collections benannte Felder im JSON speichern:

```json
[
  { "label": "Meiste Spiele:", "value": "142", "playerIds": [3] },
  { "label": "Höchste Punktzahl:", "value": "240", "playerIds": [5] }
]
```

Links werden zur Laufzeit aus den `playerIds` gebaut – nicht vorgebaut gespeichert. Die Resource braucht dann nur noch `->only(...)`, kein manuelles Index-Mapping.

---

## Aufwand und Risiko

| | Einschätzung |
|---|---|
| Aufwand | Mittel – `calculate()`, `getHighscoreCollection()`, `calcStats()` + Migration der DB-Daten |
| Risiko | Mittel – alle Seiten die `records`/`stats` anzeigen müssen angepasst werden |
| Nutzen | Hoch für spätere Migrationen, geringer Sofortnutzen |

**Empfehlung:** Nicht isoliert angehen, sondern wenn die Gruppe- oder Statistik-Seite migriert wird. Dann lohnt sich der Schnitt, weil Frontend und Backend gleichzeitig berührt werden.
