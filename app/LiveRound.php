<?php

namespace App;

use App\Live\Deck;
use App\Live\Spieler;
use App\Live\Stich;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LiveRound
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LiveGame[] $liveGames
 * @property-read int|null $live_games_count
 * @property-read \App\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LiveRound extends Model
{
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
        return $this->hasMany(LiveGame::class);
    }

    public function currentLiveGame()
    {
        return $this->liveGames()->latest()->first();
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

        $spielerIDs = $activePlayers->pluck('id');
        $spielerIDsInaktiv = $this->round->getInactivePlayers()->pluck('id');
        $spielerIndize = $activePlayers->pluck('pivot.index');

        $this->liveGames()->create([
            'live_round_id' => $this->id,
            'vorhand' => $activePlayers->first()->pivot->index,
            'dran' => $activePlayers->first()->pivot->player_id,
            'phase' => 0,
            'aktuellerStich' => new Stich(),
            'letzterStich' => new Stich(),
            'spielerIDs' => $spielerIDs,
            'spielerIndize' => $spielerIndize,
            'spielerIDsInaktiv' => $spielerIDsInaktiv,
        ]);
    }
}
