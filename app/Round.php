<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Round extends Model {

    protected $fillable = ['created_by'];

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

    public function games()
    {
        return $this->hasMany(Game::class);
    }
	
	public function createdBy()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)->withTimestamps()->withPivot('index');
    }
}
