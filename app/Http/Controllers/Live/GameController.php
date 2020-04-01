<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Live\Deck;
use App\Live\Karte;
use App\Live\Tisch;
use App\Live\Spieler;
use App\LiveGame;
use App\LiveRound;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function karteSpielen(LiveGame $liveGame, Request $request)
    {
        $this->authorize('update', $liveGame);

        // Checke, ob Karte gespielt werden durfte
        // - ist der spieler am Zug
        // - besitzt er selbst die Karte
        // - ist Karte erlaubt (spielbar)

        $karte = Karte::create($request->get('karte'));

        $liveGame->karteLegen($karte);

        if ($liveGame->aktuellerStich->count() >= 4)
        {
            // Wenn aktueller Stich = 4 Karten
            // - wer gewinnt den Stich
            // - Stich an die Person geben
            // - Stich auf letzten Stich kopieren
            $liveGame->stichVerteilen();
        }


        // Wenn es der 12. Stich war:
        // - Spiel beenden
        // - Punkte zählen
        // - Ergebnise anzeigen und eintragen

        // Wer legt die nächste Karte?
        // - war es der letzte Stich?

        // Für jeden Spieler berechnen welcher Karte er legen darf
        // Karten sortieren

        $liveGame->save();

        return 'success';
    }
}
