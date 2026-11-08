/** Farbe einer Karte in der Live-Engine: 1 Karo, 2 Herz, 3 Pik, 4 Kreuz (`app/Live/Karte.php`). */
export type LiveCardSuit = 1 | 2 | 3 | 4;

/** Wert einer Karte in der Live-Engine: 1 Neun, 2 Bube, 3 Dame, 4 König, 5 Zehn, 6 Ass. */
export type LiveCardRank = 1 | 2 | 3 | 4 | 5 | 6;

/** Der Teil einer Engine-Karte, den die Darstellung braucht. */
export type LiveCard = {
  farbe: LiveCardSuit;
  wert: LiveCardRank;
};
