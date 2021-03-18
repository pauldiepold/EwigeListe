<?php

namespace App;

use App\Live\Anzeige;
use App\Live\Deck;
use App\Live\Spieler;
use App\Live\Stich;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class LiveRound extends Model
{
    protected $attributes = [
        'schweinchen' => true,
        'fuchsSticht' => true,
        'schweinchenTrumpfsolo' => false,
        'koenigsSolo' => true,
        'karlchen' => true,
        'karlchenFangen' => true,
    ];

    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function round()
    {
        return $this->hasOne(Round::class);
    }

    public function liveGames()
    {
        return $this->hasMany(LiveGame::class)->latest();
    }

    public function currentLiveGame()
    {
        if ($this->liveGames->count() == 0)
        {
            return null;
        } else
        {
            $liveGame = $this->liveGames->first();

            return !$liveGame->beendet ? $liveGame : null;
        }
    }

    public function lastLiveGame()
    {
        if ($this->liveGames->count() == 0)
        {
            return null;
        } else
        {
            $liveGame = $this->liveGames->first();

            return $liveGame->beendet ? $liveGame : null;
        }
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, LiveGame::class);
    }

    public function starteNeuesSpiel()
    {
        if ($this->liveGames->where('beendet', 0)->count() != 0)
        {
            //abort(422, 'Es darf keine geöffneten Spiele geben!');
        }

        $activePlayers = $this->round->getActivePlayers(false);

        $spielerIDsInaktiv = $this->round->getInactivePlayers()->pluck('id');

        foreach ($activePlayers as $key => $player)
        {
            $spielerString = 'spieler' . $key;

            $$spielerString = new Spieler(
                $player->id,
                $player->surname . ' ' . $player->name,
                $player->pivot->index,
            );
        }

        $liveGame = $this->liveGames()->create([
            'live_round_id' => $this->id,
            'vorhand' => $activePlayers->first()->pivot->player_id,
            'dran' => $activePlayers->first()->pivot->player_id,
            'aktuellerStich' => new Stich(),
            'letzterStich' => new Stich(),
            'spielerIDsInaktiv' => $spielerIDsInaktiv,
            'spieler0' => $spieler0,
            'spieler1' => $spieler1,
            'spieler2' => $spieler2,
            'spieler3' => $spieler3,
            'anzeige' => new Anzeige($activePlayers),
            'messages' => collect(),
            'winners' => collect(),
            'stiche' => collect(),
        ]);

        $liveGame->kartenGeben();
        $liveGame->moeglicheVorbehalteBerechnen();
        $liveGame->phase = 2;

        $liveGame->save();

        return $liveGame;
    }
}
