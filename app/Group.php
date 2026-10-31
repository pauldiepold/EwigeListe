<?php

namespace App;

use App\Jobs\UpdateGroup;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Group extends Model
{
    use HasFactory;

    public const EWIGE_LISTE_ID = 1;

    /**
     * Rekorde werden aus den Profilen abgeleitet: Bestwert unter allen Profilen, die den
     * Mindestwert überschreiten.
     *
     * @var list<array{label: string, column: string, function: 'max'|'min', minimumColumn: string, minimum: int, unit: string}>
     */
    private const RECORD_DEFINITIONS = [
        ['label' => 'Meiste Spiele:', 'column' => 'games', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
        ['label' => 'Höchste Punktzahl:', 'column' => 'highestPoints', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
        ['label' => 'Niedrigste Punktzahl:', 'column' => 'lowestPoints', 'function' => 'min', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
        ['label' => 'Höchste Gewinnrate:', 'column' => 'winrate', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 50, 'unit' => '%'],
        ['label' => 'Niedrigste Gewinnrate:', 'column' => 'winrate', 'function' => 'min', 'minimumColumn' => 'games', 'minimum' => 50, 'unit' => '%'],
        ['label' => 'Höchste Solo-Gewinnrate:', 'column' => 'soloWinrate', 'function' => 'max', 'minimumColumn' => 'soli', 'minimum' => 10, 'unit' => '%'],
        ['label' => 'Niedrigste Solo-Gewinnrate:', 'column' => 'soloWinrate', 'function' => 'min', 'minimumColumn' => 'soli', 'minimum' => 10, 'unit' => '%'],
        ['label' => 'Wenigste Spiele bis Solo:', 'column' => 'soloRate', 'function' => 'min', 'minimumColumn' => 'soli', 'minimum' => 10, 'unit' => ''],
        ['label' => 'Meiste Spiele bis Solo:', 'column' => 'soloRate', 'function' => 'max', 'minimumColumn' => 'soli', 'minimum' => 10, 'unit' => ''],
        ['label' => 'Längste Sieges-Strähne:', 'column' => 'winStreak', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
        ['label' => 'Längste Pech-Strähne:', 'column' => 'loseStreak', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
        ['label' => 'Meiste Spiele an einem Tag:', 'column' => 'mostGamesDay', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
        ['label' => 'Meiste Spiele in einem Monat:', 'column' => 'mostGamesMonth', 'function' => 'max', 'minimumColumn' => 'games', 'minimum' => 0, 'unit' => ''],
    ];

    protected $fillable = [
        'name',
        'created_by',
    ];

    protected $hidden = [
        'queued',
        'records',
        'stats',
        'mostGames',
        'highestPoints',
        'lowestPoints',
        'highestWinrate',
        'lowestWinrate',
        'highestSoloWinrate',
        'lowestSoloWinrate',
        'highestSoloRate',
        'lowestSoloRate',
        'highestWinstreak',
        'highestLosestreak',
        'mostGamesDay',
        'mostGamesMonth',
        'totalGames',
        'pointsPerGame',
        'gamesPerRound',
    ];

    public function updateStats($date = null)
    {
        if (! $this->queued) {
            $this->queued = true;
            $this->save();
            UpdateGroup::dispatch($this, $date);
        }
    }

    public static function updateManyStats(Collection $groups, $date = null)
    {
        foreach ($groups as $group) {
            if (! $group->queued) {
                $group->queued = true;
                $group->save();
                UpdateGroup::dispatch($group, $date);
            }
        }
    }

    protected $casts = [
        'pointsPerGame' => 'decimal:1',
        'gamesPerRound' => 'decimal:1',
    ];

    /**
     * Eine Liste ohne Runden hat nur den aktuellen Monat.
     */
    public function monthsSinceFirstRound(): CarbonPeriod
    {
        $firstRoundAt = $this->rounds()->min('rounds.created_at') ?? Carbon::now();

        return CarbonPeriod::create(Carbon::parse($firstRoundAt)->startOfMonth(), '1 month', Carbon::now());
    }

    public function calculateBadgesByMonth(Carbon $date)
    {
        $pointsBadge = Badge::firstOrCreate([
            'group_id' => $this->id,
            'year' => $date->year,
            'month' => $date->month,
            'type' => 'points',
        ], [

        ]);
        $pointsBadge->calculate();

        $gamesBadge = Badge::firstOrCreate([
            'group_id' => $this->id,
            'year' => $date->year,
            'month' => $date->month,
            'type' => 'games',
        ], [

        ]);
        $gamesBadge->calculate();
    }

    public function calculate($date = null)
    {
        $date = isset($date) ? $date : Carbon::now();
        $this->calculateBadgesByMonth($date);
        $this->calculateStatistics();

        $this->save();
    }

    private function calculateStatistics(): void
    {
        $games = Game::whereHas('round.groups', function (Builder $query) {
            $query->where('groups.id', '=', $this->id);
        });

        $this->totalGames = $games->count();
        $this->pointsPerGame = round($games->avg('points') ?? 0, 1);

        $roundsCount = $this->rounds()->count();
        $this->gamesPerRound = $roundsCount === 0 ? 0 : round($this->totalGames / $roundsCount, 1);
    }

    /**
     * @return Collection<int, array{label: string, value: string, profiles: Collection<int, Profile>}>
     */
    public function recordRows(): Collection
    {
        return collect(self::RECORD_DEFINITIONS)
            ->map(function (array $definition): ?array {
                $candidates = $this->profiles
                    ->where($definition['minimumColumn'], '>', $definition['minimum'])
                    ->whereNotNull($definition['column']);

                if ($candidates->isEmpty()) {
                    return null;
                }

                $value = (float) $candidates->{$definition['function']}($definition['column']);

                return [
                    'label' => $definition['label'],
                    'value' => $value.$definition['unit'],
                    'profiles' => $candidates
                        ->filter(fn (Profile $profile): bool => (float) $profile->{$definition['column']} === $value)
                        ->values(),
                ];
            })
            ->filter()
            ->values();
    }

    public function addPlayer($player)
    {
        $this->players()->save($player);
    }

    /**
     * @param  Collection<int, int>  $playerIds
     */
    public function hasAnyPlayer(Collection $playerIds): bool
    {
        return $this->players()->whereIn('players.id', $playerIds->all())->exists();
    }

    public function addPlayers(Collection $players)
    {
        $this->players()->saveMany($players->diff($this->players));
    }

    public function hasMember(Player $player): bool
    {
        return $this->players()->whereKey($player->id)->exists();
    }

    public function canBeJoinedBy(Player $player): bool
    {
        return ! $this->closed && ! $this->hasMember($player);
    }

    public function canBeLeftBy(Player $player): bool
    {
        return $this->id !== self::EWIGE_LISTE_ID
            && ! $this->closed
            && $this->hasMember($player)
            && $this->players()->count() > 1
            && ! $this->rounds()->whereHas('players', fn (Builder $query) => $query->whereKey($player->id))->exists();
    }

    public function canBeClosedBy(User $user): bool
    {
        return $this->id !== self::EWIGE_LISTE_ID
            && ($this->creator->is($user->player) || $user->isAdmin());
    }

    public function path()
    {
        return route('groups.show', ['group' => $this->id]);
    }

    public function creator()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'profiles')->withTimestamps()->using(Profile::class);
    }

    public function rounds()
    {
        return $this->belongsToMany(Round::class)->withTimestamps();
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->where('player_id', '!=', 0);
    }

    public function games() {}
}
