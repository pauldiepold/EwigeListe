<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Live\Deck;
use App\Live\Karte;
use App\Live\Stich;
use App\Live\Spieler;
use App\LiveGame;
use App\LiveRound;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function kartenGeben(LiveGame $liveGame)
    {
        $this->authorize('update', $liveGame);

        if ($liveGame->phase != 0)
        {
            abort(422, 'falsche Phase!');
        }

        $liveGame->kartenGeben();

        $liveGame->phase = 1;

        $liveGame->save();
    }

    public function karteSpielen(LiveGame $liveGame, Request $request)
    {
        $this->authorize('update', $liveGame);

        // Karte validieren
        $karte = Karte::create((object) $request->get('karte'));

        $liveGame->istSpielerDran();
        $liveGame->istSpielerAktiv();
        $liveGame->besitztSpielerKarte($karte);
        $liveGame->istKarteSpielbar($karte);

        $liveGame->karteSpielen($karte);

        if ($liveGame->aktuellerStich->count() == 4)
        {
            $liveGame->stichVerteilen();
        }

        if ($liveGame->spielBeendet())
        {
            $liveGame->beendet = true;

            // Punkte zählen
            // Ergebnisse anzeigen und eintragen

            $liveGame->save();
            return 'Spiel beendet';
        }

        $liveGame->naechstenSpielerBerechnen();

        $liveGame->spielbareKartenBerechnen();

        $liveGame->kartenSortieren();

        $liveGame->save();

        return 'success';
    }
}
