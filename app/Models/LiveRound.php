<?php

namespace App\Models;

use App\Live\Anzeige;
use App\Live\Spieler;
use App\Live\Stich;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class LiveRound extends Model
{
    /**
     * Die Regeln einer Live-Runde mit ihrem Standard (alle an außer Super-Schwein im Trumpfsolo).
     *
     * @var array<string, bool>
     */
    public const array RULE_DEFAULTS = [
        'schweinchen' => true,
        'fuchsSticht' => true,
        'schweinchenTrumpfsolo' => false,
        'koenigsSolo' => true,
        'karlchen' => true,
        'karlchenFangen' => true,
    ];

    /**
     * Abhängige Regel => übergeordnete Regel. Ist die übergeordnete aus, ist die abhängige es auch.
     *
     * @var array<string, string>
     */
    public const array RULE_PARENTS = [
        'fuchsSticht' => 'schweinchen',
        'schweinchenTrumpfsolo' => 'schweinchen',
        'karlchenFangen' => 'karlchen',
    ];

    protected $attributes = self::RULE_DEFAULTS;

    protected $guarded = [];

    protected function casts(): array
    {
        return array_map(fn (): string => 'boolean', self::RULE_DEFAULTS);
    }

    /**
     * Ergänzt fehlende Regeln um den Standard und schaltet Regeln aus, deren übergeordnete Regel aus ist.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, bool>
     */
    public static function normalizeRules(array $input): array
    {
        $rules = [];

        foreach (self::RULE_DEFAULTS as $rule => $default) {
            $rules[$rule] = (bool) ($input[$rule] ?? $default);
        }

        foreach (self::RULE_PARENTS as $rule => $parent) {
            $rules[$rule] = $rules[$rule] && $rules[$parent];
        }

        return $rules;
    }

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
        if ($this->liveGames->count() == 0) {
            return null;
        } else {
            $liveGame = $this->liveGames->first();

            return ! $liveGame->beendet ? $liveGame : null;
        }
    }

    public function lastLiveGame()
    {
        if ($this->liveGames->count() == 0) {
            return null;
        } else {
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
        if ($this->liveGames->where('beendet', 0)->count() != 0) {
            // abort(422, 'Es darf keine geöffneten Spiele geben!');
        }

        $activePlayers = $this->round->getActivePlayers(false);

        $spielerIDsInaktiv = $this->round->getInactivePlayers()->pluck('id');

        foreach ($activePlayers as $key => $player) {
            $spielerString = 'spieler'.$key;

            $$spielerString = new Spieler($player);
        }

        $liveGame = new LiveGame;

        $liveGame->live_round_id = $this->id;
        $liveGame->vorhand = $activePlayers->first()->pivot->player_id;
        $liveGame->dran = $activePlayers->first()->pivot->player_id;
        $liveGame->aktuellerStich = new Stich;
        $liveGame->letzterStich = new Stich;
        $liveGame->spielerIDsInaktiv = $spielerIDsInaktiv;
        $liveGame->spieler0 = $spieler0;
        $liveGame->spieler1 = $spieler1;
        $liveGame->spieler2 = $spieler2;
        $liveGame->spieler3 = $spieler3;
        $liveGame->anzeige = new Anzeige($activePlayers);
        $liveGame->messages = collect();
        $liveGame->winners = collect();
        $liveGame->stiche = collect();

        $liveGame->kartenGeben();
        $liveGame->moeglicheVorbehalteBerechnen();
        $liveGame->phase = 2;

        $liveGame->save();

        return 0;
    }
}
