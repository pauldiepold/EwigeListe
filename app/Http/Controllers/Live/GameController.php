<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Http\Requests\KarteSpielen;
use App\Live\Deck;
use App\Live\Karte;
use App\Live\Stich;
use App\Live\Spieler;
use App\LiveGame;
use App\LiveRound;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function reloadData(LiveGame $liveGame)
    {
        return response()
            ->json([
                'ich' => $liveGame->getSpieler(),
                'liveGame' => $liveGame
            ]);
    }

    public function kartenGeben(LiveGame $liveGame)
    {
        abort_if($liveGame->phase != 0, 422, 'Falsche Phase!');

        $liveGame->kartenGeben();

        $liveGame->moeglicheVorbehalteBerechnen();

        $liveGame->phase = 1;

        $liveGame->save();
    }

    public function gesund(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 1, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'gesund' => 'required|boolean'
        ]);
        $gesund = $validated['gesund'];

        $liveGame->setGesund($gesund);

        $liveGame->naechstenSpielerBerechnen();

        if ($liveGame->dran == $liveGame->vorhand)
        {
            $liveGame->phase = 2;

            $liveGame->naechstenSpielerBerechnenPhase2();
        }

        $liveGame->save();
    }

    public function vorbehalt(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 2, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'vorbehalt' => [
                'required', 'string', Rule::in([
                    'Schmeißen',
                    'Hochzeit',
                    'Stille Hochzeit',
                    'Armut',
                    'Fleischlos',
                    'Bubensolo',
                    'Damensolo',
                    'Königssolo'
                ])
            ]
        ]);

        $vorbehalt = $validated['vorbehalt'];

        $liveGame->istVorbehaltMoeglich($vorbehalt);
        $liveGame->setVorbehalt($vorbehalt);

        $liveGame->naechstenSpielerBerechnenPhase2();

        $liveGame->save();
    }

    public function karteSpielen(LiveGame $liveGame, KarteSpielen $request)
    {
        abort_if($liveGame->phase != 4, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validated();
        $karte = $liveGame->getKarteVonSpieler($validated['karte']['id']);

        $liveGame->kartenSortieren();
        $liveGame->istKarteSpielbar($karte);


        $liveGame->karteSpielen($karte);

        if ($liveGame->aktuellerStich->count() == 4)
        {
            $liveGame->stichVerteilen();
        }

        if ($liveGame->spielBeendet())
        {
            $liveGame->beendet = true;
            $liveGame->phase = 101;

            $liveGame->punkteZaehlen();

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
