<?php

namespace App;

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
    public function round()
    {
        return $this->hasOne(Round::class);
    }

    public function liveGames()
    {
        return $this->hasMany(LiveGame::class);
    }

    public function currentLiveGame() {
        return $this->liveGames()->latest()->first();
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, LiveGame::class);
    }
}
