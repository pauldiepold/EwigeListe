<?php

namespace App;

use App\Live\Deck;
use App\Live\Karte;
use App\Events\LiveGameSaved;
use App\Live\Spieler;
use App\Live\Tisch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\LiveGame
 *
 * @property int $id
 * @property int $live_round_id
 * @property \Illuminate\Support\Collection $spielerIDs
 * @property \Illuminate\Support\Collection $spielerIndize
 * @property string $spieler0
 * @property string $spieler1
 * @property string $spieler2
 * @property string $spieler3
 * @property int $phase
 * @property int $vorhand
 * @property int $dran
 * @property \Illuminate\Support\Collection $letzterStich
 * @property \Illuminate\Support\Collection $aktuellerStich
 * @property bool $closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read mixed $tisch
 * @property-read \App\LiveRound $liveRound
 * @property-read \App\Round $round
 * @mixin \Eloquent
 */
class LiveGame extends Model
{
    protected $dispatchesEvents = [
        'saved' => LiveGameSaved::class
    ];

    protected $hidden = [
        /*'spieler0',
        'spieler1',
        'spieler2',
        'spieler3',*/
    ];

    protected $guarded = [];

    protected $attributes = [
        'closed' => false,
        'spieler0' => '',
        'spieler1' => '',
        'spieler2' => '',
        'spieler3' => '',
        'spielerIDs' => '',
        'spielerIndize' => '',
        'letzterStich' => '',
        'aktuellerStich' => '',
        'phase' => 1,
        'dran' => 0,
        'vorhand' => 0,
    ];

    protected $casts = [
        'spieler0' => 'object',
        'spieler1' => 'object',
        'spieler2' => 'object',
        'spieler3' => 'object',
        'spielerIDs' => 'collection',
        'spielerIndize' => 'collection',
        'letzterStich' => 'collection',
        'aktuellerStich' => 'collection',
        'closed' => 'boolean',
    ];

    public function getSpieler0Attribute($value)
    {
        return Spieler::create(
            $this->castAttribute('spieler0', $value)
        );
    }

    public function getSpieler1Attribute($value)
    {
        return Spieler::create(
            $this->castAttribute('spieler1', $value)
        );
    }

    public function getSpieler2Attribute($value)
    {
        return Spieler::create(
            $this->castAttribute('spieler2', $value)
        );
    }

    public function getSpieler3Attribute($value)
    {
        return Spieler::create(
            $this->castAttribute('spieler3', $value)
        );
    }

    public function game()
    {
        return $this->hasOne(Game::class);
    }

    public function round()
    {
        return $this->hasOneThrough(Round::class, Game::class);
    }

    public function liveRound()
    {
        return $this->belongsTo(LiveRound::class);
    }

    public function karteLegen($karte)
    {
        $spieler = $this->getSpieler();
        $spieler->karteAusHandEntfernen($karte);
        $this->setSpieler($spieler);

        $this->karteAufAktuellenStich($karte);
    }

    private function karteAufAktuellenStich($karte)
    {
        $this->aktuellerStich = $this->aktuellerStich->push($karte);
    }

    public function getSpieler($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spielerString = $this->getSpielerString($spielerID);

        return $this->$spielerString;
    }

    public function setSpieler($spieler)
    {
        $spielerString = $this->getSpielerString($spieler->player_id);
        $this->$spielerString = $spieler;
    }

    public function getSpielerString($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();

        return 'spieler' . $this->spielerIDs->search($spielerID);
    }

    public function stichVerteilen()
    {
        $this->letzterStich = $this->aktuellerStich;

        $spieler = $this->getSpieler(rand(0,3));
        $spieler->stichNehmen($this->aktuellerStich);
        $this->setSpieler($spieler);

        $this->aktuellerStich = collect();
    }
}
