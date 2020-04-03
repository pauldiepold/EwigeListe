<?php

namespace App;

use App\Live\Deck;
use App\Live\Karte;
use App\Events\LiveGameSaved;
use App\Live\Spieler;
use App\Live\Stich;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\LiveGame
 *
 * @property int $id
 * @property int $live_round_id
 * @property \Illuminate\Support\Collection $spielerIDs
 * @property \Illuminate\Support\Collection $spielerIDsInaktiv
 * @property \Illuminate\Support\Collection $spielerIndize
 * @property object $spieler0
 * @property object $spieler1
 * @property object $spieler2
 * @property object $spieler3
 * @property int $phase
 * @property int $vorhand
 * @property int $dran
 * @property object $letzterStich
 * @property object $aktuellerStich
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read mixed $aktueller_stich
 * @property-read mixed $letzter_stich
 * @property-read \App\LiveRound $liveRound
 * @property-read \App\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereAktuellerStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereBeendet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereDran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereLetzterStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpielerIDs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpielerIDsInaktiv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpielerIndize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereVorhand($value)
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
        'beendet' => false,
        'spieler0' => '',
        'spieler1' => '',
        'spieler2' => '',
        'spieler3' => '',
        'spielerIDs' => '',
        'spielerIDsInaktiv' => '',
        'spielerIndize' => '',
        'letzterStich' => '',
        'aktuellerStich' => '',
        'phase' => 0,
        'dran' => 0,
        'vorhand' => 0,
    ];

    protected $casts = [
        'spieler0' => 'object',
        'spieler1' => 'object',
        'spieler2' => 'object',
        'spieler3' => 'object',
        'spielerIDs' => 'collection',
        'spielerIDsInaktiv' => 'collection',
        'spielerIndize' => 'collection',
        'letzterStich' => 'object',
        'aktuellerStich' => 'object',
        'beendet' => 'boolean',
    ];

    public function getAktuellerStichAttribute($value)
    {
        return Stich::create(
            $this->castAttribute('aktuellerStich', $value)
        );
    }

    public function getLetzterStichAttribute($value)
    {
        return Stich::create(
            $this->castAttribute('letzterStich', $value)
        );
    }

    public function getSpieler0Attribute($value)
    {
        //dd($this->castAttribute('spieler0', $value));
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

    public function kartenGeben()
    {
        $deck = new Deck();

        for ($i = 0; $i < 4; $i++)
        {
            $spielerString = 'spieler' . $i;

            $player = Player::find($this->spielerIDs->get($i));

            $this->$spielerString = new Spieler(
                $player->id,
                $player->surname . ' ' . $player->name,
                $this->spielerIndize->get($i),
                $deck->deck->get($i)->take(2)
            );
        }
    }

    public function karteSpielen($karte)
    {
        $spieler = $this->getSpieler();
        $spieler->karteAusHandEntfernen($karte);
        $this->spielerSpeichern($spieler);

        $karte->gespieltVon = auth()->id();

        $this->karteAufAktuellenStichLegen($karte);
    }

    public function stichVerteilen()
    {
        if ($this->aktuellerStich->count() != 4)
        {
            abort(422, 'Stich hat noch keine 4 Karten!');
        }

        $stich = $this->aktuellerStich;
        $stecher = $this->werGewinntStich($stich);

        $stich->stecher = $stecher->player_id;

        $this->letzterStich = $stich;
        $this->aktuellerStich = new Stich();

        $stecher->stichNehmen($stich);
        $this->spielerSpeichern($stecher);
    }

    public function werGewinntStich($stich)
    {
        return $this->getSpieler($this->spielerIDs->random());
    }

    public function naechstenSpielerBerechnen()
    {
        if ($this->aktuellerStich->count() == 0)
        {
            $this->dran = $this->letzterStich->stecher;
        } else
        {
            $index = $this->spielerIDs->search($this->dran);
            if ($index >= 3)
            {
                $this->dran = $this->spielerIDs->get(0);
            } else
            {
                $this->dran = $this->spielerIDs->get($index + 1);
            }
        }
    }

    public function spielbareKartenBerechnen()
    {
        foreach($this->spielerIDs as $spielerID) {
            $spieler = $this->getSpieler($spielerID);
            $hand = $spieler->hand;
        }
    }

    public function kartenSortieren()
    {
        foreach($this->spielerIDs as $spielerID) {
            $spieler = $this->getSpieler($spielerID);
            $hand = $spieler->hand;
        }
    }

    private function karteAufAktuellenStichLegen($karte)
    {
        $aktuellerStich = $this->aktuellerStich;
        $aktuellerStich->push($karte);
        $this->aktuellerStich = $aktuellerStich;
    }

    public function getSpieler($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spielerString = $this->getSpielerString($spielerID);

        return $this->$spielerString;
    }

    public function getSpielerByIndex($spielerIndex)
    {
        $spielerString = $this->getSpielerStringByIndex($spielerIndex);

        return $this->$spielerString;
    }

    public function getSpielerStringByIndex($spielerIndex)
    {
        return 'spieler' . $this->spielerIndize->search($spielerIndex);
    }

    public function getSpielerString($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();

        return 'spieler' . $this->spielerIDs->search($spielerID);
    }

    public function spielerSpeichern($spieler)
    {
        $spielerString = $this->getSpielerString($spieler->player_id);
        $this->$spielerString = $spieler;
    }

    public function getKarteVonSpieler(Karte $karte, $spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spieler = $this->getSpieler($spielerID);

        return $spieler->hand->where('id', $karte->id)->first();
    }

    public function besitztSpielerKarte(Karte $karte, $spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();

        if ($this->getKarteVonSpieler($karte, $spielerID) == null)
        {
            abort(422, 'Spieler besitzt diese Karte nicht!');
        }
    }

    public function istKarteSpielbar(Karte $karte, $spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();

        if ($this->getKarteVonSpieler($karte, $spielerID)->spielbar == false)
        {
            abort(422, 'Diese Karte darf nicht gespielt werden!');
        }
    }

    public function istSpielerDran($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        if ($this->dran != $spielerID)
        {
            abort(422, 'Du bist nicht dran!');
        }
    }

    public function istSpielerAktiv($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        if ($this->spielerIDsInaktiv->contains($spielerID))
        {
            abort(422, 'Du setzt gerade aus!');
        }
    }

    public function spielBeendet()
    {
        $uebrigeKarten = 0;
        foreach($this->spielerIDs as $spielerID) {
            $uebrigeKarten += $this->getSpieler($spielerID)->hand->count();
        }

        return $uebrigeKarten == 0;
    }
}
