<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Round extends Model {

    protected $fillable = [];

    protected $attributes = [
        'old_id' => null,
    ];

    public function getLastGame()
    {
        return $this->games()->latest()->first();
    }

    public function getDealerIndex()
    {
        return $this->games->where('solo', 0)->count() % $this->players->count();
    }

    public function getActivePlayers()
    {
        $dealerIndex = $this->getDealerIndex();

        $playerIndices = collect();

        for ($i = 0; $i < 4; $i++)
        {
            if ($dealerIndex < $this->players->count() - 1)
            {
                $dealerIndex++;
            } else
            {
                $dealerIndex = 0;
            }
            $playerIndices->push($dealerIndex);
        }

        $playerIndicesSorted = $playerIndices->sort();
        $playerIndicesSorted->values()->all();

        $players = collect();

        foreach ($playerIndicesSorted as $playerIndex)
        {
            $playerID = DB::table('player_round')
                ->where('round_id', $this->id)
                ->where('index', $playerIndex)
                ->first()->player_id;

            $players->push(Player::find($playerID));
        }

        return $players;
    }

    public function addGame($winners, $pointsRound)
    {
        $players = $this->getActivePlayers();
        $solo = (count($winners) != 2 ? true : false);

        $game = Game::create([
            'points' => $pointsRound,
            'solo' => $solo,
        ]);
        $game->round()->associate($this)->save();

        foreach ($players as $player)
        {
            if (count($winners) == 1 &&
                in_array($player->id, $winners))           // Solo gewonnen
            {
                $soloist = true;
                $won = true;
                $points = 3 * $pointsRound;
            } elseif (count($winners) == 3 &&
                      !in_array($player->id, $winners))    // Solo verloren
            {
                $soloist = true;
                $won = false;
                $points = -3 * $pointsRound;
            } elseif ((count($winners) == 2 &&
                       in_array($player->id, $winners)) ||
                      (count($winners) == 3 &&
                       in_array($player->id, $winners)))    // Normalspiel gewonnen - Gegen Solo gewonnen
            {
                $soloist = false;
                $won = true;
                $points = 1 * $pointsRound;
            } elseif ((count($winners) == 2 &&
                       !in_array($player->id, $winners)) ||
                      (count($winners) == 1 &&
                       !in_array($player->id, $winners)))   // Normalspiel verloren - Gegen Solo verloren
            {
                $soloist = false;
                $won = false;
                $points = -1 * $pointsRound;
            }

            $game->players()->attach($player->id, [
                'won' => $won,
                'soloist' => $soloist,
                'points' => $points
            ]);
        }
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)->withTimestamps()->withPivot('index');
    }
}
