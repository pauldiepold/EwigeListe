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

        $liveGame->phase = 2;

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
                    'Gesund',
                    'Schmeißen',
                    'Hochzeit',
                    'Stille Hochzeit',
                    'Armut',
                    'Fleischlos',
                    'Bubensolo',
                    'Damensolo',
                    'Königssolo',
                    'Trumpfsolo',
                    'Farbsolo Herz',
                    'Farbsolo Pik',
                    'Farbsolo Kreuz',
                ])
            ]
        ]);

        $vorbehalt = $validated['vorbehalt'];

        $liveGame->istVorbehaltMoeglich($vorbehalt);
        $liveGame->setVorbehalt($vorbehalt);

        $liveGame->naechstenSpielerBerechnen();

        if ($liveGame->dran == $liveGame->spielerIDs->get(0))
        {
            $liveGame->vorbehalteAbhandeln();
        }

        $liveGame->save();
    }

    public function armutAbgeben(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 3, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'karten' => 'required|array|size:3',
            'karten.*' => 'required|array',
            'karte.*.id' => 'required|integer|between:0,47',
        ]);

        $karten = collect();
        foreach ($validated['karten'] as $karte)
        {
            $karten->push($liveGame->getKarteVonSpieler($karte['id']));
        }

        $liveGame->armutKartenAbgeben($karten);
        $liveGame->naechstenSpielerBerechnen();
        $liveGame->phase = 32;

        $liveGame->save();
    }

    public function armutMitnehmen(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 32, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'mitnehmen' => 'required|boolean',
        ]);

        if ($validated['mitnehmen'])
        {
            $spieler = $liveGame->getSpieler();
            $armutSpieler = $liveGame->getSpielerByPosition($liveGame->vorbehalte->search('Armut'));

            $hand = $spieler->hand->concat($armutSpieler->armutKarten);

            $spieler->hand = $hand;

            $liveGame->spielerSpeichern($spieler);

            $liveGame->dran = $spieler->id;

            $liveGame->phase = 33;
        } else
        {
            $liveGame->naechstenSpielerBerechnen();

            if ($liveGame->dran == $liveGame->getSpielerByPosition($liveGame->vorbehalte->search('Armut'))->id)
            {
                $liveGame->schmeissen();
            }
        }
        $liveGame->save();
    }

    public function armutZurueckgeben(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 33, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'karten' => 'required|array|size:3',
            'karten.*' => 'required|array',
            'karte.*.id' => 'required|integer|between:0,47',
        ]);

        $karten = collect();
        foreach ($validated['karten'] as $karte)
        {
            $karten->push($liveGame->getKarteVonSpieler($karte['id']));
        }

        $liveGame->armutKartenZurueckgebenUndReSetzen($karten);

        $liveGame->dran = $liveGame->vorhand;
        $liveGame->spielStarten();

        $liveGame->save();
    }

    public function karteSpielen(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 4, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'karte' => 'required|array',
            'karte.id' => 'required|integer|between:0,47'
        ]);
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

            $liveGame->wertungBerechnen();
            $liveGame->spielErgebnisUebertragen();

            $liveGame->save();

            return 'Spiel beendet';
        }

        $liveGame->naechstenSpielerBerechnen();
        $liveGame->spielbareKartenBerechnen();
        $liveGame->kartenSortieren();
        $liveGame->moeglicheAnAbsagenBerechnen();

        $liveGame->save();

        return 'success';
    }

    public function ansage(LiveGame $liveGame, Request $request)
    {
        abort_if($liveGame->phase != 4, 422, 'Falsche Phase!');

        $liveGame->istSpielerAktiv();
        $liveGame->istSpielerDran();

        $validated = $request->validate([
            'ansage' => [
                'required',
                Rule::in(['Schwarz', 30, 60, 90, 'Re', 'Kontra'])
            ]
        ]);

        $ansage = $validated['ansage'];

        if ($ansage == 'Re' || $ansage == 'Kontra')
        {
            $liveGame->ansageMachen();
        } else {
            $absage = $ansage == 'Schwarz' ? 0 : intval($ansage);
            $liveGame->absageMachen($absage);
        }

        $liveGame->moeglicheAnAbsagenBerechnen();

        $liveGame->save();

        return 'success';
    }
}
