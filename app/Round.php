<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Round
 *
 * @property int $id
 * @property int|null $live_round_id
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Player $createdBy
 * @property-read Collection|Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $path
 * @property-read mixed $players_string
 * @property-read Collection|Group[] $groups
 * @property-read int|null $groups_count
 * @property-read LiveRound|null $liveRound
 * @property-read Collection|Player[] $players
 * @property-read int|null $players_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
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
            if (auth()->id()) {
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

    public function isLive(): bool
    {
        return $this->live_round_id !== null;
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
        return $this->dealerIndexAfter($this->games);
    }

    private function dealerIndexAfter(SupportCollection $games): int
    {
        return $games
            ->where('solo', 0)
            ->where('misplay', 0)
            ->count()
            % $this->players->count();
    }

    /**
     * Der Geber, mit dem ein Spiel dieser Runde gespielt wurde; bei Altdaten ohne gespeicherten Wert aus den Spielen davor abgeleitet.
     */
    public function getDealerIndexOf(Game $game): int
    {
        return $game->dealerIndex ?? $this->dealerIndexAfter($this->games->where('id', '<', $game->id));
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
        return $this->getActivePlayersForDealer($this->getDealerIndex(), $sortPlayers);
    }

    /**
     * Die aktiven Spieler eines Spiels, das mit dem Geber auf dem Sitz $dealerIndex gespielt wurde.
     */
    public function getActivePlayersForDealer(int $dealerIndex, bool $sortPlayers = true)
    {
        $countPlayers = $this->players->count();

        if ($countPlayers == 4 || $countPlayers == 5) {
            $increments = collect([1, 2, 3, 4]);
        } elseif ($countPlayers == 6) {
            $increments = collect([1, 2, 4, 5]);
        } elseif ($countPlayers == 7) {
            $increments = collect([1, 3, 5, 6]);
        }

        $playerIndices = collect();
        foreach ($increments as $increment) {
            $currentDealerIndex = $increment + $dealerIndex;
            if ($currentDealerIndex >= $countPlayers) {
                $currentDealerIndex -= $countPlayers;
            }
            $playerIndices->push($currentDealerIndex);
        }

        if ($sortPlayers) {
            $playerIndices = $playerIndices->sort();
            $playerIndices = $playerIndices->values();
        }

        $players = collect();

        foreach ($playerIndices as $playerIndex) {
            $players->push($this->players->where('pivot.index', $playerIndex)->first());
        }

        return $players;
    }

    /**
     * Die aktiven Spieler aus Sicht des eingeloggten Spielers am Tisch: Er selbst steht vorn, wer
     * aussetzt, beginnt beim nächsten aktiven Sitz. Ohne Platz in der Runde gilt die Sitzreihenfolge.
     */
    public function getActivePlayersRotatedForUser(?int $dealerIndex = null): SupportCollection
    {
        $activePlayers = $this->getActivePlayersForDealer($dealerIndex ?? $this->getDealerIndex())->values();
        $userPlayer = $this->players->firstWhere('id', Auth::user()->player->id);

        if ($userPlayer === null) {
            return $activePlayers;
        }

        $firstIndex = $activePlayers->search(
            fn (Player $player): bool => $player->pivot->index >= $userPlayer->pivot->index
        ) ?: 0;

        return $activePlayers->slice($firstIndex)->concat($activePlayers->take($firstIndex))->values();
    }

    public function addNewGame($winners, $pointsRound, $misplay = false, $liveGameID = null)
    {
        $game = Game::create([
            'points' => $pointsRound,
            'solo' => $this->isSolo($winners, $misplay),
            'misplay' => $misplay,
            'dealerIndex' => $this->getDealerIndex(),
            'created_by' => Auth::user()->player->id,
            'round_id' => $this->id,
            'live_game_id' => $liveGameID,
        ]);

        $this->scoreGame($game, $winners, $pointsRound, $misplay);

        $this->unsetRelation('games');

        return $game;
    }

    /**
     * Ändert Gewinner, Punkte und Fehlspiel eines Spiels; Geber, Spieler, Zeitstempel und Eintragender bleiben.
     */
    public function updateGame(Game $game, $winners, $pointsRound, $misplay = false): Game
    {
        DB::transaction(function () use ($game, $winners, $pointsRound, $misplay): void {
            $game->update([
                'points' => $pointsRound,
                'solo' => $this->isSolo($winners, $misplay),
                'misplay' => $misplay,
            ]);
            $game->players()->detach();

            $this->scoreGame($game, $winners, $pointsRound, $misplay);
        });

        $this->unsetRelation('games');

        return $game;
    }

    private function isSolo($winners, bool $misplay): bool
    {
        return ! $misplay && count($winners) != 2;
    }

    private function scoreGame(Game $game, $winners, $pointsRound, bool $misplay): void
    {
        foreach ($this->getActivePlayersForDealer($this->getDealerIndexOf($game)) as $player) {
            if (count($winners) == 1 &&
                in_array($player->id, $winners)) {           // Solo gewonnen
                $soloist = true;
                $won = true;
                $points = 3 * $pointsRound;
                $misplayed = false;
            } elseif (count($winners) == 3 &&
                ! in_array($player->id, $winners)) {    // Solo verloren
                $soloist = $misplay ? false : true;
                $won = false;
                $points = -3 * $pointsRound;
                $misplayed = $misplay ? true : false;
            } elseif ((count($winners) == 2 &&
                    in_array($player->id, $winners)) ||
                (count($winners) == 3 &&
                    in_array($player->id, $winners))) {    // Normalspiel gewonnen - Gegen Solo gewonnen
                $soloist = false;
                $won = true;
                $points = 1 * $pointsRound;
                $misplayed = false;
            } elseif ((count($winners) == 2 &&
                    ! in_array($player->id, $winners)) ||
                (count($winners) == 1 &&
                    ! in_array($player->id, $winners))) {   // Normalspiel verloren - Gegen Solo verloren
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

    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps()
            ->withCount('rounds')
            ->orderByRaw('rounds_count desc');
    }

    public function profiles()
    {
        return $this->profilesIn($this->groups);
    }

    /**
     * @param  Collection<int, Group>  $groups
     */
    public function profilesIn(Collection $groups): Collection
    {
        return Profile::whereIn('group_id', $groups->pluck('id'))
            ->whereIn('player_id', $this->players->pluck('id'))
            ->get();
    }

    public function liveRound()
    {
        return $this->belongsTo(LiveRound::class);
    }
}
