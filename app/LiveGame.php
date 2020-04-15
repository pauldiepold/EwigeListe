<?php

namespace App;

use App\Live\Anzeige;
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
 * @property \Illuminate\Support\Collection $spielerIDsInaktiv
 * @property object $spieler0
 * @property object $spieler1
 * @property object $spieler2
 * @property object $spieler3
 * @property object $anzeige
 * @property int $phase
 * @property int $stichNr
 * @property int $vorhand
 * @property int $dran
 * @property string $spieltyp
 * @property object $letzterStich
 * @property object $aktuellerStich
 * @property bool|null $gewinntRe
 * @property int|null $wertungsPunkte
 * @property string $wertung
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read mixed $absagen
 * @property-read mixed $aktueller_stich
 * @property-read mixed $ansagen
 * @property-read mixed $armut_karten
 * @property-read mixed $letzter_stich
 * @property-read mixed $res
 * @property-read mixed $spieler
 * @property-read mixed $spieler_i_ds
 * @property-read mixed $spieler_indize
 * @property-read mixed $vorbehalte
 * @property-read \App\LiveRound $liveRound
 * @property-read \App\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereAktuellerStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereAnzeige($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereBeendet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereDran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereGewinntRe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereLetzterStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieler3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpielerIDsInaktiv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereSpieltyp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereStichNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereVorhand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereWertung($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveGame whereWertungsPunkte($value)
 * @mixin \Eloquent
 */
class LiveGame extends Model
{
    protected $dispatchesEvents = [
        'saved' => LiveGameSaved::class
    ];

    protected $hidden = [
        'anzeige',
        'spieler0',
        'spieler1',
        'spieler2',
        'spieler3',
    ];

    protected $appends = [
        'vorbehalte',
        'ansagen',
        'absagen',
        'res',
        'spielerIDs',
        'spielerIndize',
        'spieler'
    ];

    protected $guarded = [];

    protected $attributes = [
        'beendet' => false,
        'spieler0' => '',
        'spieler1' => '',
        'spieler2' => '',
        'spieler3' => '',
        'spielerIDsInaktiv' => '',
        'letzterStich' => '',
        'aktuellerStich' => '',
        'spieltyp' => '',
        'phase' => 0,
        'stichNr' => 0,
        'dran' => 0,
        'vorhand' => 0,
        'gewinntRe' => null,
        'wertungsPunkte' => null,
        'wertung' => '',
    ];

    protected $casts = [
        'spieler0' => 'object',
        'spieler1' => 'object',
        'spieler2' => 'object',
        'spieler3' => 'object',
        'anzeige' => 'object',
        'spielerIDsInaktiv' => 'collection',
        'letzterStich' => 'object',
        'aktuellerStich' => 'object',
        'beendet' => 'boolean',
        'gewinntRe' => 'boolean'
    ];

    public function getArmutKartenAttribute($value)
    {
        $collection = $this->castAttribute('armutKarten', $value);
        $karten = $collection->map(function ($item, $key)
        {
            return Karte::create((object) $item);
        });

        return $karten;
    }

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

    public function getAnzeigeAttribute($value)
    {
        return Anzeige::create(
            $this->castAttribute('anzeige', $value)
        );
    }

    public function getSpielerAttribute($value)
    {
        return $this->anzeige->spieler;
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

    public function getSpielerIDsAttribute()
    {
        $spielerIDs = collect();
        $spielerIDs->push($this->spieler0->id);
        $spielerIDs->push($this->spieler1->id);
        $spielerIDs->push($this->spieler2->id);
        $spielerIDs->push($this->spieler3->id);

        return $spielerIDs;
    }

    public function getSpielerIndizeAttribute()
    {
        $spielerIndize = collect();
        $spielerIndize->push($this->spieler0->index);
        $spielerIndize->push($this->spieler1->index);
        $spielerIndize->push($this->spieler2->index);
        $spielerIndize->push($this->spieler3->index);

        return $spielerIndize;
    }

    public function getVorbehalteAttribute()
    {
        $vorbehalte = collect();
        $vorbehalte->push($this->spieler0->vorbehalt);
        $vorbehalte->push($this->spieler1->vorbehalt);
        $vorbehalte->push($this->spieler2->vorbehalt);
        $vorbehalte->push($this->spieler3->vorbehalt);

        return $vorbehalte;
    }

    public function getAnsagenAttribute()
    {
        $ansagen = collect();
        $ansagen->push($this->spieler0->ansage);
        $ansagen->push($this->spieler1->ansage);
        $ansagen->push($this->spieler2->ansage);
        $ansagen->push($this->spieler3->ansage);

        return $ansagen;
    }

    public function getAbsagenAttribute()
    {
        $absagen = collect();
        $absagen->push($this->spieler0->absage);
        $absagen->push($this->spieler1->absage);
        $absagen->push($this->spieler2->absage);
        $absagen->push($this->spieler3->absage);

        return $absagen;
    }

    public function getResAttribute()
    {
        $res = collect();
        $res->put($this->spieler0->id, $this->spieler0->isRe);
        $res->put($this->spieler1->id, $this->spieler1->isRe);
        $res->put($this->spieler2->id, $this->spieler2->isRe);
        $res->put($this->spieler3->id, $this->spieler3->isRe);

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
            $spieler = $this->getSpieler($spielerID);
            $spieler->hand = $deck->getkarten($key);
            $this->spielerSpeichern($spieler);
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
        abort_if($this->aktuellerStich->count() != 4, 422, 'Stich hat noch keine 4 Karten!');

        $stich = $this->aktuellerStich;
        $stecher = $this->werGewinntStich($stich);

        $stich->stecher = $stecher->id;
        $stecher->stichNehmen($stich);
        $this->spielerSpeichern($stecher);

        $this->letzterStich = $stich;
        $this->aktuellerStich = new Stich();

        $this->checkenObGeheiratet();

        $this->stichNr = $this->stichNr + 1;
    }

    public function checkenObGeheiratet()
    {
        if ($this->spieltyp == 'Hochzeit' &&
            $this->res->filter(function ($value, $key)
            {
                return $value === true;
            })->count() == 1)
        {
            $heirater = $this->getSpielerByPosition($this->vorbehalte->search('Hochzeit', true));
            $stecher = $this->getSpieler($this->letzterStich->stecher);

            if ($heirater->id != $stecher->id)
            {
                // Geheiratet
                $stecher->isRe = true;

                if ($heirater->ansage === true)
                {
                    $stecher->ansage = true;
                } elseif ($stecher->ansage === true)
                {
                    $heirater->ansage = true;
                }

                if ($stecher->absage !== null)
                {
                    $heirater->absage = $stecher->absage;
                } elseif ($heirater->absage !== null)
                {
                    $stecher->absage = $heirater->absage;
                }

                $this->spielerSpeichern($heirater);
                $this->spielerSpeichern($stecher);
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
        if ($vorbehalt == 'Gesund') return;

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

    public function istHochzeit($hand)
    {
        return $hand->where('rang', 22)->count() == 2;
    }

    public function istSchmeissen($hand)
    {
        $neunen = $hand->where('wert', 1);
        $fuenfOderMehrNeunen = $neunen->count() >= 5;
        $vierUnterschiedlicheNeunen = $neunen->unique('farbe')->count() >= 4;

        return $fuenfOderMehrNeunen || $vierUnterschiedlicheNeunen;
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

    public function vorbehalteAbhandeln()
    {
        $vorbehaltIndex = null;
        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt === 'Bubensolo' ||
                $vorbehalt === 'Damensolo' ||
                $vorbehalt === 'Königssolo' ||
                $vorbehalt === 'Fleischlos')
            {
                $this->loescheVorbehalteExcept($key);
                $this->soloSpielen($key, $vorbehalt);

                return;
            }
        }

        if ($this->vorbehalte->containsStrict('Schmeißen'))
        {
            $this->schmeissen();

            return;
        }

        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt === 'Armut')
            {
                $this->armutSpielen($key);

                return;
            }
        }

        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt === 'Hochzeit')
            {
                $this->hochzeitSpielen($key);

                return;
            }
            if ($vorbehalt === 'Stille Hochzeit')
            {
                $this->soloSpielen($key, $vorbehalt);

                return;
            }
        }

        $this->normalspielSpielen();
    }

    public function loescheVorbehalteExcept($position)
    {
        //abort(422, 'loesche vorbehalte');
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

        $this->alleSpielerKontraSetzen($spieler->id);

        $this->dran = $spieler->id;
        $this->spieltyp = $vorbehalt;

        $this->spielStarten();
    }

    public function schmeissen()
    {
        foreach($this->spielerIDs as $spielerID) {
            $spieler = $this->getSpieler($spielerID);

            $spieler->hand = collect();
            $spieler->stiche = collect();
            $spieler->armutKarten = collect();

            $spieler->moeglicheVorbehalte = collect();
            $spieler->vorbehalt = null;
            $spieler->isRe = null;
            $spieler->ansage = null;
            $spieler->absage = null;
            $spieler->punkte = null;

            $this->spielerSpeichern($spieler);
        }

        $this->dran = $this->vorhand;
        $this->phase = 0;
    }

    public function armutSpielen($position)
    {
        $this->phase = 3;
        $this->loescheVorbehalteExcept($position);

        $this->spieltyp = 'Armut';

        $armutSpieler = $this->getSpielerByPosition($position);

        $this->dran = $armutSpieler->id;
    }

    public function hochzeitSpielen($position)
    {
        $this->spieltyp = 'Hochzeit';

        $spieler = $this->getSpielerByPosition($position);
        $spieler->isRe = true;

        $this->alleSpielerKontraSetzen();

        $this->spielerSpeichern($spieler);

        $this->spielStarten();
    }

    public function normalspielSpielen()
    {
        $this->spieltyp = 'Normalspiel';

        $this->setReUndKontra();

        $this->spielStarten();
    }

    public function spielStarten()
    {
        $this->stichNr = 1;
        $this->phase = 4;
        $this->trumpfBerechnen();
        $this->kartenSortieren();
        $this->spielbareKartenBerechnen();
    }

    public function alleSpielerKontraSetzen($except = null)
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            if ($spielerID == $except) continue;

            $spieler = $this->getSpieler($spielerID);
            if (!$spieler->isRe)
            {
                $spieler->isRe = false;
                $this->spielerSpeichern($spieler);
            }
        }
    }

    public function setReUndKontra()
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);
            $kreuzdamen = $spieler->hand->where('rang', 22);
            if ($kreuzdamen->count() == 1)
            {
                $spieler->isRe = true;
            } elseif ($kreuzdamen->count() == 0)
            {
                $spieler->isRe = false;
            } else
            {
                abort(422, 'Spieler kann keine 2 Kreuzdamen haben, da Normalspiel');
            }
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

            $trumpf = $hand->where('trumpf', true)->sortByDesc('id');
            $trumpf = $trumpf->values();

            $farbe = $hand->where('trumpf', false);
            $karo = $farbe->where('farbe', 1)->sortByDesc('wert');
            $pik = $farbe->where('farbe', 3)->sortByDesc('wert');
            $herz = $farbe->where('farbe', 2)->sortByDesc('wert');
            $kreuz = $farbe->where('farbe', 4)->sortByDesc('wert');

            $hand = $trumpf->concat($karo)->concat($herz)->concat($pik)->concat($kreuz);

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

    public function wertungBerechnen()
    {
        $rePunkte = 0;
        $reAnsage = null;
        $reAbsage = null;
        $reKarten = collect();
        $kontraPunkte = 0;
        $kontraAnsage = null;
        $kontraAbsage = null;
        $kontraKarten = collect();

        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);
            if ($spieler->isRe)
            {
                $rePunkte += $spieler->punkte;

                foreach ($spieler->stiche as $stich)
                {
                    $reKarten = $reKarten->concat($stich->karten);
                }

                if ($spieler->ansage) $reAnsage = true;
                if ($spieler->absage) $reAbsage = $spieler->absage;
            } else
            {
                $kontraPunkte += $spieler->punkte;

                foreach ($spieler->stiche as $stich)
                {
                    $kontraKarten = $kontraKarten->concat($stich->karten);
                }

                if ($spieler->ansage) $kontraAnsage = true;
                if ($spieler->absage) $kontraAbsage = $spieler->absage;
            }
        }

        /*$rePunkte = 130;
        $reAnsage = true;
        $reAbsage = null;
        $kontraPunkte = 240 - $rePunkte;
        $kontraAnsage = null;
        $kontraAbsage = null;*/

        abort_if($kontraAbsage != null && $reAbsage != null, 422, 'Hör auf du Spacko, du hast keine Ahnung wie das Spiel funktioniert!');

        $punktegrenze = 120;
        if ($reAbsage != null)
        {
            $punktegrenze = 240 - $reAbsage;
            $absage = $reAbsage;
        } elseif ($kontraAbsage != null)
        {
            $punktegrenze = $kontraAbsage;
            $absage = $kontraAbsage;
        }

        $punkteString = '';
        $wertungsPunkte = 0;

        $gewinntRe = $rePunkte > $punktegrenze ? true : false;

        /* **** Gewonnen **** */
        if ($rePunkte != 120) $wertungsPunkte++;
        if ($rePunkte != 120) $punkteString .= '+1 Gewonnen<br>';

        /* **** Gegen die Alten **** */
        if ($gewinntRe == false) $wertungsPunkte++;
        if ($gewinntRe == false) $punkteString .= '+1 Gegen die Alten<br>';

        /* **** Re **** */
        if ($reAnsage) $wertungsPunkte += 2;
        if ($reAnsage) $punkteString .= '+2 Re angesagt<br>';

        /* **** Kontra **** */
        if ($kontraAnsage) $wertungsPunkte += 2;
        if ($kontraAnsage) $punkteString .= '+2 Kontra angesagt<br>';

        /* **** Absagen **** */
        if ($gewinntRe && $reAbsage || !$gewinntRe && $kontraAbsage)
        {
            $absagePunkte = (120 - $absage) / 30 * 2;
            $wertungsPunkte += $absagePunkte;

            $punkteString .= '+' . $absagePunkte . ' Keine ' . $absage . ' angesagt<br>';
        } elseif ($gewinntRe && $kontraAbsage || !$gewinntRe && $reAbsage)
        {
            $absagePunkte = (120 - $absage) / 30;

            $gewinnerAugen = $gewinntRe ? $rePunkte : $kontraPunkte;

            $absagePunkte += (int) floor(($gewinnerAugen - $absage) / 30);
            $wertungsPunkte += $absagePunkte;

            $punkteString .= '+' . $absagePunkte . ' Keine ' . $absage . ' angesagt<br>';
        }


        /* **** Fuchs gefangen **** */
        foreach ($reKarten as $karte)
        {
            if ($karte->rang == 14)
            {
                $spieler = $this->getSpieler($karte->gespieltVon);
                if ($spieler->isRe == false)
                {
                    if ($gewinntRe)
                    {
                        $wertungsPunkte++;
                        $punkteString .= '+1 Fuchs gefangen<br>';
                    } else
                    {
                        $wertungsPunkte--;
                        $punkteString .= '-1 Fuchs gefangen<br>';
                    }
                }
            }
        }
        foreach ($kontraKarten as $karte)
        {
            if ($karte->rang == 14)
            {
                $spieler = $this->getSpieler($karte->gespieltVon);
                if ($spieler->isRe == true)
                {
                    if ($gewinntRe)
                    {
                        $wertungsPunkte--;
                        $punkteString .= '-1 Fuchs gefangen<br>';
                    } else
                    {
                        $wertungsPunkte++;
                        $punkteString .= '+1 Fuchs gefangen<br>';
                    }
                }
            }
        }


        /* **** Karlchen **** */
        $hoechsterRang = $this->letzterStich->karten->max('rang');
        $hoechsteKarte = $this->letzterStich->karten->where('rang', $hoechsterRang)->first();
        $karlchen = $this->letzterStich->karten->where('rang', 18);

        $reMachtKarlchen = false;
        $kontraMachtKarlchen = false;
        $reFaengtKarlchen = 0;
        $kontraFaengtKarlchen = 0;

        if ($karlchen->count() > 0)
        {
            if ($hoechsteKarte->rang == 18 && $karlchen->count() == 1) // Karlchen gemacht
            {
                if ($this->getSpieler($hoechsteKarte->gespieltVon)->isRe)
                {
                    $reMachtKarlchen = true;
                } else
                {
                    $kontraMachtKarlchen = true;
                }
            } elseif ($hoechsteKarte->rang == 18 && $karlchen->count() == 2) // Karlchen gemacht und Karlchen fängt Karlchen
            {
                $erstesKarlchenSpieler = $this->getSpieler($karlchen->first()->gespieltVon);
                $zweitesKarlchenSpieler = $this->getSpieler($karlchen->last()->gespieltVon);

                if ($erstesKarlchenSpieler->isRe)
                {
                    $reMachtKarlchen = true;
                } else
                {
                    $kontraMachtKarlchen = true;
                }

                if ($erstesKarlchenSpieler->isRe != $zweitesKarlchenSpieler->isRe)
                {
                    if ($erstesKarlchenSpieler->isRe)
                    {
                        $reFaengtKarlchen = 1;
                    } else
                    {
                        $kontraFaengtKarlchen = 1;
                    }
                }
            } elseif ($hoechsteKarte->rang != 18 && $karlchen->count() == 1) // Karlchen gefangen
            {
                $hoechsteKarteSpieler = $this->getSpieler($hoechsteKarte->gespieltVon);
                $karlchenSpieler = $this->getSpieler($karlchen->first()->gespieltVon);

                if ($hoechsteKarteSpieler->isRe != $karlchenSpieler->isRe)
                {
                    if ($hoechsteKarteSpieler->isRe)
                    {
                        $reFaengtKarlchen++;
                    } else
                    {
                        $kontraFaengtKarlchen++;
                    }
                }
            } elseif ($hoechsteKarte->rang != 18 && $karlchen->count() == 2) // Zwei Karlchen gefangen
            {
                $hoechsteKarteSpieler = $this->getSpieler($hoechsteKarte->gespieltVon);
                $erstesKarlchenSpieler = $this->getSpieler($karlchen->first()->gespieltVon);
                $zweitesKarlchenSpieler = $this->getSpieler($karlchen->last()->gespieltVon);

                if ($hoechsteKarteSpieler->isRe != $erstesKarlchenSpieler->isRe)
                {
                    if ($hoechsteKarteSpieler->isRe)
                    {
                        $reFaengtKarlchen++;
                    } else
                    {
                        $kontraFaengtKarlchen++;
                    }
                }

                if ($hoechsteKarteSpieler->isRe != $zweitesKarlchenSpieler->isRe)
                {
                    if ($hoechsteKarteSpieler->isRe)
                    {
                        $reFaengtKarlchen++;
                    } else
                    {
                        $kontraFaengtKarlchen++;
                    }
                }
            }
        }

        if ($gewinntRe)
        {
            if ($reMachtKarlchen)
            {
                $wertungsPunkte++;
                $punkteString .= '+1 Karlchen<br>';
            }
            if ($kontraMachtKarlchen)
            {
                $wertungsPunkte--;
                $punkteString .= '-1 Karlchen<br>';
            }
            if ($reFaengtKarlchen)
            {
                $wertungsPunkte += $reFaengtKarlchen;
                $punkteString .= '+' . $reFaengtKarlchen . ' Karlchen gefangen<br>';
            }
            if ($kontraFaengtKarlchen)
            {
                $wertungsPunkte -= $kontraFaengtKarlchen;
                $punkteString .= '-' . $kontraFaengtKarlchen . ' Karlchen gefangen<br>';
            }
        } else
        {
            if ($reMachtKarlchen)
            {
                $wertungsPunkte--;
                $punkteString .= '-1 Karlchen<br>';
            }
            if ($kontraMachtKarlchen)
            {
                $wertungsPunkte++;
                $punkteString .= '+1 Karlchen<br>';
            }
            if ($reFaengtKarlchen)
            {
                $wertungsPunkte -= $reFaengtKarlchen;
                $punkteString .= '-' . $reFaengtKarlchen . ' Karlchen gefangen<br>';
            }
            if ($kontraFaengtKarlchen)
            {
                $wertungsPunkte += $kontraFaengtKarlchen;
                $punkteString .= '+' . $kontraFaengtKarlchen . ' Karlchen gefangen<br>';
            }
        }

        /* **** Doppelkopf **** */
        $alleStiche = collect();
        foreach ($this->spielerIDs as $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);
            $alleStiche = $alleStiche->concat($spieler->stiche);
        }
        foreach ($alleStiche as $stich)
        {
            if ($stich->punkteZaehlen() >= 40)
            {
                $stecher = $this->getSpieler($stich->stecher);
                if ($stecher->isRe && $gewinntRe
                    || !$stecher->isRe && !$gewinntRe)
                {
                    $wertungsPunkte++;
                    $punkteString .= '+1 Doppelkopf<br>';
                } else
                {
                    $wertungsPunkte--;
                    $punkteString .= '-1 Doppelkopf<br>';
                }
            }
        }

        $punkteString = 'Kontra: ' . $kontraPunkte . '<br>' . $punkteString;
        $punkteString = 'Re: ' . $rePunkte . '<br>' . $punkteString;

        if ($gewinntRe)
        {
            $punkteString = '<b>Re</b> hat gewonnen mit ' . $wertungsPunkte . ' Punkten!<br><br>' . $punkteString;
        } else
        {
            $punkteString = '<b>Kontra</b> hat gewonnen mit ' . $wertungsPunkte . ' Punkten!<br><br>' . $punkteString;
        }


        $this->gewinntRe = $gewinntRe;
        $this->wertungsPunkte = $wertungsPunkte;
        $this->wertung = $punkteString;
    }

    public function spielErgebnisUebertragen()
    {
        if ($this->gewinntRe)
        {
            $winners = $this->res->filter(function ($value, $key)
            {
                return $value === true;
            })->keys()->toArray();
        } else
        {
            $winners = $this->res->filter(function ($value, $key)
            {
                return $value === true;
            })->keys()->toArray();
        }
        $round = $this->liveRound->round;
        $game = $round->addNewGame($winners, $this->wertungsPunkte);
    }

    public function setVorbehalt($vorbehalt)
    {
        $spieler = $this->getSpieler();

        if ($vorbehalt == 'Gesund')
        {
            $spieler->vorbehalt = true;
        } else
        {
            $spieler->vorbehalt = $vorbehalt;
        }

        $this->spielerSpeichern($spieler);
    }

    public function ansageMachen()
    {
        $spieler = $this->getSpieler();

        //abort_if($this->stichNr > 2 || $spieler->hand->count() < 11, 422, 'Es kann keine Ansage mehr gemacht werden.');

        if ($spieler->ansage === null)
        {
            $spieler->ansage = true;
            $this->spielerSpeichern($spieler);

            $anzeige = $this->anzeige;
            $ansage = $spieler->isRe ? 'Re' : 'Kontra';
            $anzeige->set('ansage', $spieler->id, $ansage);
            $this->anzeige = $anzeige;

            $this->mitspielerSagtAuchAn();
        } else
        {
            abort(422, 'Du hast bereits eine Ansage gemacht!');
        }
    }

    public function absageMachen($zahl)
    {
        $spieler = $this->getSpieler();

        // To-Do check ob zu diesem Zeitpunkt noch abgesagt werden darf
        //abort_if($this->stichNr > 2 || $spieler->hand->count() < 11, 422, 'Es kann keine Ansage mehr gemacht werden.');
        abort_if($spieler->ansage === null, 422, 'Du musst erst eine Ansage machen!');

        if ($zahl < 90)
        {
            abort_if($spieler->absage != $zahl + 30, 422, 'Du musst zuvor Absagen');
        }

        $spieler->absage = $zahl;
        $this->spielerSpeichern($spieler);

        $this->mitspielerSagtAuchAb();

        $anzeige = $this->anzeige;
        $anzeige->set('absage', $spieler->id, $zahl);
        $this->anzeige = $anzeige;
    }

    public function mitspielerSagtAuchAn()
    {
        $spieler = $this->getSpieler();

        foreach ($this->res as $spielerID => $isRe)
        {
            if ($isRe === $spieler->isRe && $spielerID != $spieler->id)
            {
                $mitspieler = $this->getSpieler($spielerID);
                $mitspieler->ansage = $spieler->ansage;
                $this->spielerSpeichern($mitspieler);
            }
        }
    }

    public function mitspielerSagtAuchAb()
    {
        $spieler = $this->getSpieler();

        foreach ($this->res as $spielerID => $isRe)
        {
            if ($isRe === $spieler->isRe && $spielerID != $spieler->id)
            {
                $mitspieler = $this->getSpieler($spielerID);
                $mitspieler->absage = $spieler->absage;
                $this->spielerSpeichern($mitspieler);
            }
        }
    }

    public function armutKartenAbgeben(Collection $karten)
    {
        $spieler = $this->getSpieler();

        $spieler->armutKarten = $karten;

        foreach($karten as $karte) {
            $spieler->karteAusHandEntfernen($karte);
        }

        $this->spielerSpeichern($spieler);
    }

    public function armutKartenZurueckgebenUndReSetzen(Collection $karten)
    {
        $spieler = $this->getSpieler();

        foreach($karten as $karte) {
            $spieler->karteAusHandEntfernen($karte);
        }

        $armutSpieler = $this->getSpielerByPosition($this->vorbehalte->search('Armut'));
        $hand = $armutSpieler->hand->concat($karten);
        $armutSpieler->hand = $hand;

        $armutSpieler->isRe = true;
        $spieler->isRe = true;

        $this->spielerSpeichern($armutSpieler);
        $this->spielerSpeichern($spieler);

        $this->alleSpielerKontraSetzen();
    }
}