# Kartenbilder

Die Bilder in diesem Ordner gehen auf die SVG-Spielkarten von Adrian Kennard (RevK) zurück, die er 2018 unter CC0 (Public Domain) freigegeben hat: <https://revk.uk/2018/06/svg-vector-playing-cards.html>. Eine Namensnennung ist nicht erforderlich.

Geändert wurde pro Karte (`<Rang><Farbe>.svg`, z. B. `QC.svg`): kleine Eckzeichen und Hintergrund entfernt, Rot und Schwarz auf die Kartenfarben der App gesetzt, `viewBox` auf die Kartenfläche zugeschnitten, danach mit `npx svgo@3 --multipass` verkleinert. Das Eckzeichen und die Farbsymbole im Eckzeichen zeichnet `PlayingCard.vue`.
