<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Game;

class Profile extends Model {

    public function updateProfile()
    {
        $games = DB::table('game_player')->where('player_id', $this->player_id);

        $this->games = $games->count();
        $gamesWon = clone $games;
        $gamesLost = clone $games;
        $this->gamesWon = $gamesWon->where('won', 1)->count();
        $this->gamesLost = $gamesLost->where('won', '0')->count();

        $this->points = $games->sum('points');
        $this->pointsPerGame = $this->games == 0 ? null : $this->points / $this->games;
        $this->pointsPerWin = $this->games == 0 ? null : $gamesWon->sum('points') / $this->gamesWon;
        $this->pointsPerLose = $this->games == 0 ? null : $gamesLost->sum('points') / $this->gamesLost;
        $this->winrate = $this->games == 0 ? null : $this->gamesWon / $this->games * 100;

        $soli = clone $games;
        $this->soli = $soli->where('soloist', 1)->count();

        $soliWon = clone $soli;
        $soliLost = clone $soli;
        $this->soliWon = $soliWon->where('won', 1)->count();
        $this->soliLost = $soliLost->where('won', '0')->count();
        $this->soloWinrate = $this->soli == 0 ? null : $this->soliWon / $this->soli * 100;
        $this->soloPoints = $soli->sum('points');

        $groupedByDay = $this->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('d-m-Y');
        });
        $groupedByDayCounted = $groupedByDay->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();
        //$this->mostGamesDay = $groupedByDayCounted->keys()->last();
        //$this->mostGamesDayCount = $groupedByDayCounted->last();


        $this->save();
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

}
