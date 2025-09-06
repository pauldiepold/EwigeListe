<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Round
 *
 * @property int $id
 * @property int|null $live_round_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Player $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $path
 * @property-read mixed $players_string
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \App\LiveRound|null $liveRound
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $active_players
 * @property-read mixed $dealer_index
 * @property-read mixed $inactive_players
 */
class Round extends Model
{
    use HasFactory;

    protected $fillable = ['created_by', 'liveGame'];

    protected $attributes = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (auth()->id())
            {
                $model->created_by = auth()->user()->player->id;
            } else {
                $model->created_by = 1;
            }
        });
    }

    public function path()
    {
        return route('rounds.show', ['round' => $this->id]);
    }

    public function getPlayersStringAttribute()
    {
        return nice_count($this->players->pluck('surname')->toArray());
    }

    public function getPathAttribute()
    {
        return $this->path();
    }

    public function createLiveRound()
    {
        $liveRound = $this->liveRound()->create();
        $this->liveRound()->associate($liveRound)->save();
    }

    public function getLastGame()
    {
        return $this->games->last();
    }

    public function getDealerIndexAttribute()
    {
        return $this->getDealerIndex();
    }

    public function getDealerIndex()
    {
        return $this
                ->games
                ->where('solo', 0)
                ->where('misplay', 0)
                ->count()
            % $this
                ->players
                ->count();
    }

    public function getInactivePlayersAttribute()
    {
        return $this->getInactivePlayers();
    }

    public function getInactivePlayers()
    {
        return $this->players->diff($this->getActivePlayers());
    }

    public function getActivePlayersAttribute()
    {
        return $this->getActivePlayers();
    }

    public function getActivePlayers($sortPlayers = true)
    {
        $dealerIndex = $this->getDealerIndex();
        $countPlayers = $this->players->count();

        if ($countPlayers == 4 || $countPlayers == 5)
        {
            $increments = collect([1, 2, 3, 4]);
        } elseif ($countPlayers == 6)
        {
            $increments = collect([1, 2, 4, 5]);
        } elseif ($countPlayers == 7)
        {
            $increments = collect([1, 3, 5, 6]);
        }

        $playerIndices = collect();
        foreach ($increments as $increment)
        {
            $currentDealerIndex = $increment + $dealerIndex;
            if ($currentDealerIndex >= $countPlayers)
            {
                $currentDealerIndex -= $countPlayers;
            }
            $playerIndices->push($currentDealerIndex);
        }

        if ($sortPlayers)
        {
            $playerIndices = $playerIndices->sort();
            $playerIndices = $playerIndices->values();
        }

        $players = collect();

        foreach ($playerIndices as $playerIndex)
        {
            $players->push($this->players->where('pivot.index', $playerIndex)->first());
        }

        return $players;
    }

    /**
     * Rotiert die aktiven Spieler so, dass der eingeloggte User an erster Stelle steht
     * oder (wenn User nicht aktiv ist) die Person links von ihm an erster Stelle steht
     * Diese Methode ist speziell für das Frontend gedacht
     */
    public function getActivePlayersRotatedForUser()
    {
        $activePlayers = $this->getActivePlayers(false);
        
        // Sonderfall: Keine aktiven Spieler
        if ($activePlayers->isEmpty()) {
            return $activePlayers;
        }
        
        $userId = Auth::user()->player->id;
        
        // Prüfe ob User aktiv ist
        $userIndex = $activePlayers->search(function($player) use ($userId) {
            return $player->id === $userId;
        });
        
        if ($userIndex !== false) {
            // User ist aktiv: Rotiere so dass er an erster Stelle steht
            return $this->rotatePlayersFromIndex($activePlayers, $userIndex);
        }
        
        // User ist nicht aktiv: Finde die Person links von ihm
        $referencePlayer = $this->findPlayerLeftOfUser($userId);
        
        if ($referencePlayer) {
            $referenceIndex = $activePlayers->search(function($player) use ($referencePlayer) {
                return $player->id === $referencePlayer->id;
            });
            
            if ($referenceIndex !== false) {
                return $this->rotatePlayersFromIndex($activePlayers, $referenceIndex);
            }
        }
        
        // Fallback: normale Reihenfolge
        return $activePlayers->sortBy('pivot.index')->values();
    }
    
    /**
     * Rotiert eine Collection von Spielern ausgehend von einem bestimmten Index
     */
    private function rotatePlayersFromIndex($players, $startIndex)
    {
        $rotatedPlayers = collect();
        $count = $players->count();
        
        for ($i = 0; $i < $count; $i++) {
            $index = ($startIndex + $i) % $count;
            $rotatedPlayers->push($players[$index]);
        }
        
        return $rotatedPlayers;
    }
    
    /**
     * Findet die Person links vom User (im Uhrzeigersinn)
     */
    private function findPlayerLeftOfUser($userId)
    {
        $allPlayers = $this->players->sortBy('pivot.index');
        $userPlayer = $allPlayers->where('id', $userId)->first();
        
        if (!$userPlayer) {
            return null;
        }
        
        $userPivotIndex = $userPlayer->pivot->index;
        $countPlayers = $allPlayers->count();
        
        // Person links von mir (im Uhrzeigersinn)
        $leftPlayerIndex = ($userPivotIndex - 1 + $countPlayers) % $countPlayers;
        
        return $allPlayers->where('pivot.index', $leftPlayerIndex)->first();
    }

    public function addNewGame($winners, $pointsRound, $misplay = false, $liveGameID = null)
    {
        $players = $this->getActivePlayers();
        $solo = count($winners) != 2;
        $solo = $misplay ? false : $solo;

        $game = Game::create([
            'points' => $pointsRound,
            'solo' => $solo,
            'misplay' => $misplay,
            'dealerIndex' => $this->getDealerIndex(),
            'created_by' => Auth::user()->player->id,
            'round_id' => $this->id,
            'live_game_id' => $liveGameID,
        ]);

        foreach ($players as $player)
        {
            if (count($winners) == 1 &&
                in_array($player->id, $winners))           // Solo gewonnen
            {
                $soloist = true;
                $won = true;
                $points = 3 * $pointsRound;
                $misplayed = false;
            } elseif (count($winners) == 3 &&
                !in_array($player->id, $winners))    // Solo verloren
            {
                $soloist = $misplay ? false : true;
                $won = false;
                $points = -3 * $pointsRound;
                $misplayed = $misplay ? true : false;
            } elseif ((count($winners) == 2 &&
                    in_array($player->id, $winners)) ||
                (count($winners) == 3 &&
                    in_array($player->id, $winners)))    // Normalspiel gewonnen - Gegen Solo gewonnen
            {
                $soloist = false;
                $won = true;
                $points = 1 * $pointsRound;
                $misplayed = false;
            } elseif ((count($winners) == 2 &&
                    !in_array($player->id, $winners)) ||
                (count($winners) == 1 &&
                    !in_array($player->id, $winners)))   // Normalspiel verloren - Gegen Solo verloren
            {
                $soloist = false;
                $won = false;
                $points = -1 * $pointsRound;
                $misplayed = false;
            }

            $game->players()->attach($player->id, [
                'won' => $won,
                'soloist' => $soloist,
                'points' => $points,
                'misplayed' => $misplayed,
            ]);
        }

        return $game;
    }

    public function games()
    {
        return $this->hasMany(Game::class)->orderBy('id', 'asc');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withTimestamps()
            ->withPivot('index')
            ->orderBy('pivot_index');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps()
            ->withCount('rounds')
            ->orderByRaw('rounds_count desc');
    }

    public function profiles()
    {
        return Profile::whereIn('group_id', $this->groups->pluck('id'))
            ->whereIn('player_id', $this->players->pluck('id'))
            ->get();
    }

    public function liveRound()
    {
        return $this->belongsTo(LiveRound::class);
    }
}
