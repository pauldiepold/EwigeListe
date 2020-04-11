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
 * @property int $stichNr
 * @property int $vorhand
 * @property int $dran
 * @property string $spieltyp
 * @property object $letzterStich
 * @property object $aktuellerStich
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read mixed $aktueller_stich
 * @property-read mixed $gesund
 * @property-read mixed $letzter_stich
 * @property-read mixed $res
 * @property-read mixed $vorbehalte
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieltyp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereStichNr($value)
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
        'spieler0',
        'spieler1',
        'spieler2',
        'spieler3',
    ];

    protected $appends = [
        'gesund',
        'vorbehalte',
        'res',
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
        'spieltyp' => '',
        'phase' => 0,
        'stichNr' => 0,
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

    public function getGesundAttribute()
    {
        $gesund = collect();
        $gesund->push($this->spieler0->gesund);
        $gesund->push($this->spieler1->gesund);
        $gesund->push($this->spieler2->gesund);
        $gesund->push($this->spieler3->gesund);

        return $gesund;
    }

    public function getVorbehalteAttribute()
    {
        $gesund = collect();
        $gesund->push($this->spieler0->vorbehalt);
        $gesund->push($this->spieler1->vorbehalt);
        $gesund->push($this->spieler2->vorbehalt);
        $gesund->push($this->spieler3->vorbehalt);

        return $gesund;
    }

    public function getResAttribute()
    {
        $res = collect();
        $res->push($this->spieler0->isRe);
        $res->push($this->spieler1->isRe);
        $res->push($this->spieler2->isRe);
        $res->push($this->spieler3->isRe);

        return $res;
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

        foreach ($this->spielerIDs as $key => $spielerID)
        {
            $spielerString = 'spieler' . $key;

            $player = Player::find($spielerID);

            $this->$spielerString = new Spieler(
                $player->id,
                $player->surname . ' ' . $player->name,
                $this->spielerIndize->get($key),
                $deck->getKarten($key)
            );
        }

        $this->trumpfBerechnen();
        $this->kartenSortieren();
    }

    public function karteSpielen($karte)
    {
        $spieler = $this->getSpieler();
        $spieler->karteAusHandEntfernen($karte);
        $this->spielerSpeichern($spieler);

        $karte->gespieltVon = auth()->id();
        $karte->spielbar = false;

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

        $stich->stecher = $stecher->id;
        $stecher->stichNehmen($stich);

        $this->letzterStich = $stich;
        $this->aktuellerStich = new Stich();

        $this->checkenObGeheiratet();

        $this->stichNr = $this->stichNr + 1;

        $this->spielerSpeichern($stecher);
    }

    public function checkenObGeheiratet()
    {
        if ($this->spieltyp == 'Hochzeit' &&
            $this->res->contains(null))
        {
            $heirater = $this->getSpielerByPosition($this->vorbehalte->search('Hochzeit'));
            $stecher = $this->getSpieler($this->letzterStich->stecher);

            if ($heirater->id != $stecher->id)
            {
                $stecher->isRe = true;
                $this->spielerSpeichern($stecher);
                $this->restlicheSpielerKontraSetzen();
            }
        }
    }

    public function restlicheSpielerKontraSetzen()
    {
        foreach($this->spielerIDs as $spielerID) {
            $spieler = $this->getSpieler($spielerID);
            if (!$spieler->isRe) {
                $spieler->isRe = false;
                $this->spielerSpeichern($spieler);
            }
        }
    }

    public function werGewinntStich($stich)
    {
        $ersteKarte = $stich->ersteKarte();

        if ($ersteKarte->trumpf)
        {
            $siegerKarte = $this->besteKarteBestimmen($stich->karten);
        } else
        {
            $trumpfKarten = $stich->karten->where('trumpf', 1);

            if ($trumpfKarten->count() > 0)
            {
                $siegerKarte = $this->besteKarteBestimmen($trumpfKarten);
            } else
            {
                $siegerKarten = $stich->karten->where('farbe', $ersteKarte->farbe);
                $siegerKarte = $this->besteKarteBestimmen($siegerKarten, $ersteKarte->farbe);
            }
        }

        return $this->getSpieler($siegerKarte->gespieltVon);
    }

    public function besteKarteBestimmen($karten, $farbe = null)
    {
        if ($farbe === null)
        {
            $maxRang = $karten->max('rang');
            $siegerKarten = $karten->where('rang', $maxRang);

            if ($maxRang == 23 && $this->stichtZweiteHerz10DieErste())
            {
                $siegerKarte = $siegerKarten->last();
            } else
            {
                $siegerKarte = $siegerKarten->first();
            }
        } else
        { // Nur Karten einer Farbe vorhanden
            $maxWert = $karten->max('wert');
            $siegerKarte = $karten->where('wert', $maxWert)->first();
        }

        return $siegerKarte;
    }

    public function stichtZweiteHerz10DieErste()
    {
        return ($this->stichNr != 12 &&
                $this->spieltyp != 'Bubensolo' &&
                $this->spieltyp != 'Damensolo' &&
                $this->spieltyp != 'Königssolo' &&
                $this->spieltyp != 'Fleischlos');
    }

    public function moeglicheVorbehalteBerechnen()
    {
        foreach ($this->spielerIDs as $key => $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);

            $armut = $this->istArmut($spieler->hand);
            $hochzeit = $this->istHochzeit($spieler->hand);
            $schmeissen = $this->istSchmeissen($spieler->hand);

            $spieler->moeglicheVorbehalte = collect();
            if ($armut) $spieler->moeglicheVorbehalte->push('Armut');
            if ($hochzeit) $spieler->moeglicheVorbehalte->push('Hochzeit');
            if ($hochzeit) $spieler->moeglicheVorbehalte->push('Stille Hochzeit');
            if ($schmeissen) $spieler->moeglicheVorbehalte->push('Schmeißen');
            $spieler->moeglicheVorbehalte->push('Fleischlos');
            $spieler->moeglicheVorbehalte->push('Bubensolo');
            $spieler->moeglicheVorbehalte->push('Damensolo');
            $spieler->moeglicheVorbehalte->push('Königssolo');

            $this->spielerSpeichern($spieler);
        }
    }

    public function istVorbehaltMoeglich($vorbehalt)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spieler = $this->getSpieler($spielerID);

        if (!$spieler->moeglicheVorbehalte->contains($vorbehalt))
        {
            abort(422, 'Dieser Vorbehalt ist nicht möglich!');
        }
    }

    public function istArmut($hand)
    {
        $wenigerAls3Trumpf = $hand->where('trumpf', true)->count() <= 3;

        $hoechsterTrumpfFuchs = $hand->max('rang') == 14;

        return $wenigerAls3Trumpf || $hoechsterTrumpfFuchs;
    }

    private function istHochzeit($hand)
    {
        return $hand->where('rang', 22)->count() == 2;
    }

    private function istSchmeissen($hand)
    {
        $neunen = $hand->where('wert', 1);
        $fuenfOderMehrNeunen = $neunen->count() >= 5;
        $vierUnterschiedlicheNeunen = $neunen->unique('farbe')->count() >= 4;

        return $fuenfOderMehrNeunen || $vierUnterschiedlicheNeunen;
    }

    public function istSpielerGesund($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spieler = $this->getSpieler($spielerID);

        return $spieler->gesund;
    }

    public function hatSpielerVorbehaltOffengelegt($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spieler = $this->getSpieler($spielerID);

        return isset($spieler->vorbehalt);
    }

    public function naechstenSpielerBerechnen()
    {

        if ($this->phase == 4 && $this->aktuellerStich->count() == 0)
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

    public function naechstenSpielerBerechnenPhase2()
    {
        if ($this->istSpielerGesund($this->dran) == false &&
            $this->hatSpielerVorbehaltOffengelegt($this->dran) == false)
        {
            return;
        } else
        {
            $index = $this->spielerIDs->search($this->dran);
            if ($index < 3)
            {
                $this->dran = $this->spielerIDs->get($index + 1);
                $this->naechstenSpielerBerechnenPhase2();
            } else
            {
                $this->dran = $this->spielerIDs->get(0);
                $this->vorbehalteAbhandeln();
            }
        }
    }

    public function vorbehalteAbhandeln()
    {
        $vorbehaltIndex = null;
        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt == 'Bubensolo' ||
                $vorbehalt == 'Damensolo' ||
                $vorbehalt == 'Königssolo' ||
                $vorbehalt == 'Fleischlos')
            {
                $this->loescheVorbehalteExcept($key);
                $this->soloSpielen($key, $vorbehalt);

                return;
            }
        }

        if ($this->vorbehalte->contains('Schmeißen'))
        {
            $this->schmeissen();

            return;
        }

        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt == 'Armut')
            {
                $this->phase = 3;
                $this->loescheVorbehalteExcept($key);
                $this->armutSpielen($key);

                return;
            }
        }

        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt == 'Hochzeit')
            {
                $this->hochzeitSpielen($key);

                return;
            }
            if ($vorbehalt == 'Stille Hochzeit')
            {
                $this->soloSpielen($key, $vorbehalt);

                return;
            }
        }

        $this->normalspielSpielen();
    }

    public function loescheVorbehalteExcept($position)
    {
        for ($i = 0; $i <= 3; $i++)
        {
            if ($i == $position) continue;

            $spieler = $this->getSpielerByPosition($i);
            $spieler->vorbehalt = null;
            $this->spielerSpeichern($spieler);
        }
    }

    public function soloSpielen($position, $vorbehalt)
    {
        $spieler = $this->getSpielerByPosition($position);
        $spieler->isRe = true;
        $this->spielerSpeichern($spieler);

        $this->setKontraExcept($position);

        $this->dran = $spieler->id;
        $this->spieltyp = $vorbehalt;

        $this->spielStarten();
    }

    public function schmeissen()
    {
        $this->phase = 0;
    }

    public function armutSpielen($position)
    {
        $this->spieltyp = 'Normalspiel';

        $this->spielStarten();
    }

    public function hochzeitSpielen($position)
    {
        $this->spieltyp = 'Hochzeit';

        $spieler = $this->getSpielerByPosition($position);
        $spieler->isRe = true;
        $this->spielerSpeichern($spieler);

        $this->spielStarten();
    }

    public function normalspielSpielen()
    {
        $this->spieltyp = 'Normalspiel';

        $this->spielStarten();
    }

    public function spielStarten()
    {
        $this->stichNr = 1;
        $this->phase = 4;
        $this->trumpfBerechnen();
        $this->spielbareKartenBerechnen();
    }

    public function setKontraExcept($position)
    {
        for ($i = 0; $i <= 3; $i++)
        {
            if ($i == $position) continue;

            $spieler = $this->getSpielerByPosition($i);
            $spieler->isRe = false;
            $this->spielerSpeichern($spieler);
        }
    }

    public function spielbareKartenBerechnen()
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);

            $neueHand = collect();
            $hand = $spieler->hand;
            foreach ($hand as $karte)
            {
                $karte->spielbar = $this->istSpielbar($karte, $hand);
                $neueHand->push($karte);
            }

            $spieler->hand = $neueHand;
            $this->spielerSpeichern($spieler);
        }
    }

    public function istSpielbar(Karte $karte, $hand)
    {
        $ersteKarteImStich = $this->aktuellerStich->ersteKarte();

        if ($ersteKarteImStich)
        {
            if ($ersteKarteImStich->trumpf)
            {
                $hatNochTrumpf = $hand->where('trumpf', true)->count() > 0;
                if ($hatNochTrumpf)
                {
                    return $karte->trumpf;
                } else
                {
                    return true;
                }
            } else
            {
                $hatNochFarbe = $hand->where('trumpf', false)->where('farbe', $ersteKarteImStich->farbe)->count() > 0;
                if ($hatNochFarbe)
                {
                    return $karte->farbe == $ersteKarteImStich->farbe && !$karte->trumpf;
                } else
                {
                    return true;
                }
            }
        } else
        {
            return true;
        }
    }

    public function kartenSortieren()
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);
            $hand = $spieler->hand;

            $hand = $hand->sortByDesc('id');
            $hand = $hand->values();

            /*
            $trumpf = $hand->where('trumpf', true);
            $farben = $hand->where('trumpf', false);

            $herz10 = $trumpf->whereIn('id', [47, 48]);
            $buben = $trumpf->where('wert', 2)->sortByDesc('farbe');
            $damen = $trumpf->where('wert', 3)->sortByDesc('farbe');
            $karo = $trumpf->whereNotIn('wert', [2, 3])->where('farbe', 1)->sortByDesc('wert');

            $farben = $farben->sortByDesc('wert')->groupBy('farbe')->sortKeysDesc();

            $hand = $herz10->concat($damen)->concat($buben)->concat($karo)->concat($farben->flatten());
            */

            $spieler->hand = $hand;
            $this->spielerSpeichern($spieler);
        }
    }

    public function trumpfBerechnen()
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);

            $neueHand = collect();
            foreach ($spieler->hand as $karte)
            {
                $karte->trumpf = $this->istTrumpf($karte);
                $neueHand->push($karte);
            }

            $spieler->hand = $neueHand;
            $this->spielerSpeichern($spieler);
        }
    }

    public function istTrumpf(Karte $karte = null)
    {
        if (!$karte) return false;

        if ($this->spieltyp == 'Normalspiel' ||
            $this->spieltyp == 'Hochzeit' ||
            $this->spieltyp == 'Stille Hochzeit' ||
            $this->spieltyp == '')
        {
            if ($karte->wert == 2 || $karte->wert == 3) return true;
            if ($karte->farbe == 1) return true;
            if ($karte->farbe == 2 && $karte->wert == 5) return true;
        } elseif ($this->spieltyp == 'Bubensolo')
        {
            if ($karte->wert == 2) return true;
        } elseif ($this->spieltyp == 'Damensolo')
        {
            if ($karte->wert == 3) return true;
        } elseif ($this->spieltyp == 'Königssolo')
        {
            if ($karte->wert == 4) return true;
        } elseif ($this->spieltyp == 'Fleischlos')
        {
            return false;
        }

        return false;
    }

    public function karteAufAktuellenStichLegen($karte)
    {
        $aktuellerStich = $this->aktuellerStich;
        $aktuellerStich->push($karte);
        $this->aktuellerStich = $aktuellerStich;
    }

    /**
     * @param null $spielerID
     * @return Spieler
     */
    public function getSpieler($spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spielerString = $this->getSpielerString($spielerID);

        return $this->$spielerString;
    }

    /**
     * @param $spielerIndex
     * @return Spieler
     */
    public function getSpielerByIndex($spielerIndex)
    {
        $spielerString = $this->getSpielerStringByIndex($spielerIndex);

        return $this->$spielerString;
    }

    /**
     * @param $spielerIndex
     * @return Spieler
     */
    public function getSpielerByPosition($spielerPosition)
    {
        $spielerString = 'spieler' . $spielerPosition;

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
        $spielerString = $this->getSpielerString($spieler->id);
        $this->$spielerString = $spieler;
    }

    /**
     * @param $kartenID
     * @param null $spielerID
     * @return Karte
     */
    public function getKarteVonSpieler($kartenID, $spielerID = null)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spieler = $this->getSpieler($spielerID);

        $karte = $spieler->hand->where('id', $kartenID)->first();

        if (!is_a($karte, 'App\Live\Karte'))
        {
            abort(422, 'Spieler besitzt diese Karte nicht!');
        }

        return $karte;
    }

    public function istKarteSpielbar(Karte $karte)
    {
        if ($karte->spielbar == false)
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
        foreach ($this->spielerIDs as $spielerID)
        {
            $uebrigeKarten += $this->getSpieler($spielerID)->hand->count();
        }

        return $uebrigeKarten == 0;
    }

    public function punkteZaehlen()
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);
            $spieler->punkteZaehlen();
            $this->spielerSpeichern($spieler);
        }
    }

    public function setGesund($gesund)
    {
        $spieler = $this->getSpieler();
        $spieler->gesund = $gesund;
        $this->spielerSpeichern($spieler);
    }

    public function setVorbehalt($vorbehalt)
    {
        $spieler = $this->getSpieler();
        $spieler->vorbehalt = $vorbehalt;
        $this->spielerSpeichern($spieler);
    }
}
