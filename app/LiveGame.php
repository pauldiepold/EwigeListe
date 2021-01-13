<?php

namespace App;

use App\Casts\SticheCast;
use App\Events\RoundUpdated;
use App\Live\Anzeige;
use App\Live\Deck;
use App\Live\Karte;
use App\Events\LiveGameSaved;
use App\Live\Spieler;
use App\Live\Stich;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * App\LiveGame
 *
 * @property int $id
 * @property int $live_round_id
 * @property Collection $spielerIDsInaktiv
 * @property object $spieler0
 * @property object $spieler1
 * @property object $spieler2
 * @property object $spieler3
 * @property object $anzeige
 * @property int $phase
 * @property int $stichNr
 * @property int $vorhand
 * @property int $dran
 * @property Collection|null $augen
 * @property Collection|null $winners
 * @property bool|null $geschmissen
 * @property Collection|null $messages
 * @property bool|null $geheiratet
 * @property int|null $kontrasOffengelegt
 * @property int|null $resOffengelegt
 * @property string $spieltyp
 * @property object $letzterStich
 * @property object $aktuellerStich
 * @property bool|null $gewinntRe
 * @property int|null $wertungsPunkte
 * @property Collection $wertung
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game|null $game
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
 * @property-read \App\Round|null $round
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereAktuellerStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereAnzeige($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereBeendet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereDran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGeheiratet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGeschmissen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGewinntRe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontrasOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereLetzterStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereResOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpielerIDsInaktiv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieltyp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereStichNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereVorhand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWertung($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWertungsPunkte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWinners($value)
 * @mixin \Eloquent
 * @property mixed|null $stiche
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereStiche($value)
 * @property bool|null $reAnsage
 * @property bool|null $kontraAnsage
 * @property int|null $reAbsage
 * @property int|null $kontraAbsage
 * @property int|null $kontraAugen
 * @property int|null $reAugen
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAugen($value)
 * @property int|null $schweinchen
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSchweinchen($value)
 */
class LiveGame extends Model
{
    protected $dispatchesEvents = [
        'saved' => LiveGameSaved::class
    ];

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
        'geschmissen' => false,
        'schweinchen' => false,
        'geheiratet' => false,
        'resOffengelegt' => 0,
        'kontrasOffengelegt' => 0,
        'reAnsage' => false,
        'kontraAnsage' => false,
        'reAbsage' => null,
        'kontraAbsage' => null,
        'reAugen' => null,
        'kontraAugen' => null,
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
        'gewinntRe' => 'boolean',
        'messages' => 'collection',
        'geschmissen' => 'boolean',
        'geheiratet' => 'boolean',
        'winners' => 'collection',
        'wertung' => 'collection',
        'augen' => 'collection',
        'reAnsage' => 'boolean',
        'kontraAnsage' => 'boolean',
        'stiche' => SticheCast::class,
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

    public function getResAttribute()
    {
        $res = collect();
        $res->put($this->spieler0->id, $this->spieler0->isRe);
        $res->put($this->spieler1->id, $this->spieler1->isRe);
        $res->put($this->spieler2->id, $this->spieler2->isRe);
        $res->put($this->spieler3->id, $this->spieler3->isRe);

        return $res;
    }

    public function kartenGeben()
    {
        $deck = new Deck();

        foreach ($this->spielerIDs as $key => $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);
            $spieler->hand = $deck->getkarten($key);
            $spieler->hand_save = $deck->getkarten($key);
            $this->spielerSpeichern($spieler);
        }

        $this->trumpfBerechnen();
        $this->kartenSortieren();
    }

    public function karteSpielen($karte)
    {
        Log::debug('test');
        $spieler = $this->getSpieler();
        $spieler->karteAusHandEntfernen($karte);
        $this->spielerSpeichern($spieler);

        $karte->gespieltVon = auth()->id();
        $karte->spielbar = false;

        $this->karteAufAktuellenStichLegen($karte);

        if (($karte->id == 44 || $karte->id == 45) &&
            ($this->spieltyp == 'Normalspiel' || $this->spieltyp == 'Stille Hochzeit'))
        {
            if (!$spieler->parteiOffengelegt || $this->spieltyp == 'Stille Hochzeit')
            {
                $this->resOffengelegt++;
                $spieler->parteiOffengelegt++;
                $this->spielerSpeichern($spieler);
                $this->setAnzeige('partei', $spieler->id, 'Re-Partei');
            }
            $this->parteienEindeutig();
        }
    }

    public function parteienEindeutig()
    {
        if (($this->spieltyp == 'Normalspiel' && ($this->resOffengelegt >= 2 || $this->kontrasOffengelegt >= 2)) ||
            ($this->spieltyp == 'Stille Hochzeit' && ($this->resOffengelegt >= 2 || $this->kontrasOffengelegt >= 3)))
        {
            if ($this->spieltyp == 'Stille Hochzeit')
            {
                $reSpielerID = $this->res->filter(function ($value, $key) {
                    return $value;
                })->keys()->first();

                $this->setAnzeige('spieltyp', $reSpielerID, 'Stille Hochzeit');
            }
            foreach ($this->spielerIDs as $spielerID)
            {
                $spieler = $this->getSpieler($spielerID);

                $partei = $spieler->isRe ? 'Re-Partei' : 'Kontra-Partei';
                $this->setAnzeige('partei', $spieler->id, $partei);
            }
        }
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
        //$this->stiche->push($stich);

        $this->checkenObGeheiratet();

        $this->stichNr = $this->stichNr + 1;

        $this->stiche->push($stich);
    }

    public function checkenObGeheiratet()
    {
        if ($this->spieltyp == 'Hochzeit' && !$this->geheiratet &&
            $this->res->filter(function ($value, $key) {
                return $value === true;
            })->count() == 1)
        {
            $heirater = $this->getSpielerByPosition($this->vorbehalte->search('Hochzeit', true));
            $stecher = $this->getSpieler($this->letzterStich->stecher);

            if ($heirater->id != $stecher->id)
            {
                // Geheiratet
                $stecher->isRe = true;
                $this->geheiratet = true;
                $this->setAnzeige('partei', $stecher->id, 'Re-Partei');
                $this->setAnzeige('partei', $heirater->id, 'Re-Partei');
                $this->pushMessage("<b>$stecher->name:</b> hat geheiratet!");
                $this->spielerSpeichern($stecher);

                $this->alleSpielerKontraSetzen($stecher->id, true);

                $this->kontraAnsage = false;
                $this->kontraAbsage = null;

                foreach ($this->spielerIDs as $spielerID)
                {
                    $spieler = $this->getSpieler($spielerID);

                    if ($spieler->id == $heirater->id) continue;

                    if ($spieler->id == $stecher->id)
                    {
                        if ($spieler->ansage)
                        {
                            $this->reAnsage = true;
                            $this->setAnzeige('ansage', $spieler->id, 'Re');
                        }
                        if ($spieler->absage) $this->reAbsage = $spieler->absage;
                        continue;
                    }

                    if ($spieler->ansage) $this->kontraAnsage = true;
                    if ($spieler->absage) $this->kontraAbsage = $spieler->absage;
                }
            }
            if ($heirater->id == $stecher->id && $this->stichNr == 3)
            {
                // Hochzeit spielt alleine
                $this->geheiratet = true;
                $this->setAnzeige('partei', $heirater->id, 'Re-Partei');
                $this->pushMessage("<b>$heirater->name:</b> spielt alleine!");

                $this->alleSpielerKontraSetzen($stecher->id, true);
            }
        }
    }

    public function werGewinntStich($stich)
    {
        $ersteKarte = $stich->ersteKarte();

        if ($ersteKarte->trumpf)
        {
            $siegerKarte = $this->besteKarteBestimmen($stich->karten->where('trumpf', 1));
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
        if ($this->liveRound->schweinchen &&
            ($this->spieltyp == 'Normalspiel' ||
                $this->spieltyp == 'Armut' ||
                $this->spieltyp == 'Hochzeit' ||
                $this->spieltyp == 'Stille Hochzeit' ||
                ($this->spieltyp == 'Trumpfsolo' && $this->liveRound->schweinchenTrumpfsolo)))
        {
            $this->istSchweinchen($stich, $siegerKarte);
        }

        return $this->getSpieler($siegerKarte->gespieltVon);
    }

    public function istSchweinchen($stich, $siegerKarte)
    {
        if ($stich->karten->where('rang', 14)->count() > 0) // Ist ein Fuchs im Stich?
        {
            $fuchs = $stich->karten->where('rang', 14)->first();
            $spieler = $this->getSpieler($fuchs->gespieltVon);
            $hatFuchs = $spieler->hand->search(fn($item, $key) => $item->rang == 14);

            if ($hatFuchs) // Hat Fuchs-Besitzer noch einen Fuchs?
            {
                if ($this->liveRound->fuchsSticht) // Muss Fuchs Stechen für Schweinchen?
                {
                    if ($siegerKarte->rang != 14) // Hat Fuchs nicht gestochen?
                    {
                        return;
                    }
                }

                $this->schweinchen = true;
                $this->pushMessage("<b>$spieler->name:</b> Schweinchen!");
            }
        }
    }

    public function besteKarteBestimmen($karten, $farbe = null)
    {
        if ($farbe === null)
        {
            if ($this->schweinchen && $karten->where('rang', 14)->count() == 1)
            {
                $maxRang = 14;
            } else
            {
                $maxRang = $karten->max('rang');
            }

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
            $spieler->moeglicheVorbehalte->push('Trumpfsolo');
            $spieler->moeglicheVorbehalte->push('Farbsolo Herz');
            $spieler->moeglicheVorbehalte->push('Farbsolo Pik');
            $spieler->moeglicheVorbehalte->push('Farbsolo Kreuz');

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
        return $wenigerAls3Trumpf = $hand->where('trumpf', true)->count() <= 3;
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

        $keineKarteUeberFuchs = $hand->max('rang') <= 14;

        return $fuenfOderMehrNeunen || $vierUnterschiedlicheNeunen || $keineKarteUeberFuchs;
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

    public function istSolo($vorbehalt = null)
    {
        $vorbehalt = $vorbehalt ?? $this->spieltyp;

        if ($vorbehalt === 'Bubensolo' ||
            $vorbehalt === 'Damensolo' ||
            $vorbehalt === 'Königssolo' ||
            $vorbehalt === 'Fleischlos' ||
            $vorbehalt === 'Trumpfsolo' ||
            $vorbehalt === 'Farbsolo Herz' ||
            $vorbehalt === 'Farbsolo Pik' ||
            $vorbehalt === 'Farbsolo Kreuz')
        {
            return true;
        } else
        {
            return false;
        }
    }

    public function vorbehalteAbhandeln()
    {
        $vorbehaltIndex = null;
        $this->setAnzeige('vorbehalt', 0, '');

        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($this->istSolo($vorbehalt))
            {
                $this->loescheVorbehalteExcept($key);
                $this->soloSpielen($key, $vorbehalt);

                return;
            }
        }

        foreach ($this->vorbehalte as $key => $vorbehalt)
        {
            if ($vorbehalt === 'Schmeißen')
            {
                $this->schmeissen($key);

                return;
            }
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
                $this->soloSpielen($key, $vorbehalt, true);

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

    public function soloSpielen($position, $vorbehalt, $istStilleHochzeit = false)
    {
        $spieler = $this->getSpielerByPosition($position);
        $spieler->isRe = true;
        $this->spielerSpeichern($spieler);

        $this->spieltyp = $vorbehalt;

        if ($istStilleHochzeit)
        {
            $this->pushMessage("Es wird ein Normalspiel gespielt!");
            $this->alleSpielerKontraSetzen($spieler->id, false);
        } else
        {
            $this->pushMessage("<b>$spieler->name</b> spielt ein $vorbehalt!");
            $this->alleSpielerKontraSetzen($spieler->id, true);

            $this->setAnzeige('spieltyp', $spieler->id, $vorbehalt);
            $this->setAnzeige('partei', $spieler->id, 'Re-Partei');

            $this->dran = $spieler->id;
        }

        $this->spielStarten();
    }

    public function schmeissen($position = null, $niemand_armut = false)
    {
        $schmeisser = $position ? $this->getSpielerByPosition($position) : null;

        $this->geschmissen = true;
        $this->beendet = true;
        $this->phase = 101;
        $this->save();

        $newGame = $this->liveRound->starteNeuesSpiel();
        if ($niemand_armut) $newGame->pushMessage('Niemand wollte die Armut mitnehmen. Neues Spiel gestartet!');
        else $newGame->pushMessage("<b>$schmeisser->name</b> hat geschmissen!");
        $newGame->save();

        broadcast(new RoundUpdated($this->liveRound->round->id));
    }

    public function armutSpielen($position)
    {
        $this->phase = 3;
        $this->loescheVorbehalteExcept($position);

        $this->spieltyp = 'Armut';

        $armutSpieler = $this->getSpielerByPosition($position);
        $this->setAnzeige('spieltyp', $armutSpieler->id, 'Armut');
        $this->setAnzeige('partei', $armutSpieler->id, 'Re-Partei');
        $this->pushMessage("<b>$armutSpieler->name</b> spielt eine Armut!");

        $this->dran = $armutSpieler->id;
    }

    public function hochzeitSpielen($position)
    {
        $this->spieltyp = 'Hochzeit';

        $spieler = $this->getSpielerByPosition($position);
        $spieler->isRe = true;

        $this->setAnzeige('spieltyp', $spieler->id, 'Hochzeit');
        $this->pushMessage("<b>$spieler->name</b> spielt eine Hochzeit!");

        $this->alleSpielerKontraSetzen();

        $this->spielerSpeichern($spieler);

        $this->spielStarten();
    }

    public function normalspielSpielen()
    {
        $this->spieltyp = 'Normalspiel';
        $this->pushMessage("Es wird ein Normalspiel gespielt!");

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
        $this->moeglicheAnAbsagenEintragen();
    }

    public function alleSpielerKontraSetzen($except = null, $anzeige = false)
    {
        foreach ($this->spielerIDs as $spielerID)
        {
            if ($spielerID == $except) continue;

            $spieler = $this->getSpieler($spielerID);
            if (!$spieler->isRe)
            {
                $spieler->isRe = false;
                $this->spielerSpeichern($spieler);
                if ($anzeige)
                {
                    $this->setAnzeige('partei', $spieler->id, 'Kontra-Partei');
                }
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
            if ($this->schweinchen && $trumpf->where('rang', '14')->count() > 0)
            {
                $schweinchen = $trumpf->where('rang', '14')->first();
                $trumpf = $trumpf->where('rang', '!=', '14');
                $trumpf->prepend($schweinchen);
            }
            $trumpf = $trumpf->values();

            $farbe = $hand->where('trumpf', false);
            $karo = $farbe->where('farbe', 1)->sortByDesc('wert');
            $pik = $farbe->where('farbe', 3)->sortByDesc('wert');
            $herz = $farbe->where('farbe', 2)->sortByDesc('wert');
            $kreuz = $farbe->where('farbe', 4)->sortByDesc('wert');

            $hand = $trumpf->concat($karo)->concat($pik)->concat($herz)->concat($kreuz);

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
            $this->spieltyp == 'Trumpfsolo' ||
            $this->spieltyp == 'Armut' ||
            $this->spieltyp == '')
        {
            if ($karte->wert == 2 || $karte->wert == 3) return true;
            if ($karte->farbe == 1) return true;
            if ($karte->farbe == 2 && $karte->wert == 5) return true;
        } elseif (substr($this->spieltyp, 0, 8) == 'Farbsolo')
        {
            if ($karte->wert == 2 || $karte->wert == 3) return true;
            if ($karte->farbe == 2 && $karte->wert == 5) return true;

            if ($this->spieltyp == 'Farbsolo Herz')
            {
                if ($karte->farbe == 2) return true;
            } elseif ($this->spieltyp == 'Farbsolo Pik')
            {
                if ($karte->farbe == 3) return true;
            } elseif ($this->spieltyp == 'Farbsolo Kreuz')
            {
                if ($karte->farbe == 4) return true;
            }
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
    public function getSpieler($spielerID = null, $hand_save = true)
    {
        $spielerID = $spielerID ?? auth()->id();
        $spielerString = $this->getSpielerString($spielerID);

        $spieler = $this->$spielerString;
        if (!$hand_save) $spieler->hand_save = '';

        return $spieler;
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

    public function istSpielerDran($spielerID = null, $ansageVorDemSpiel = false)
    {
        $spielerID = $spielerID ?? auth()->id();
        if ($ansageVorDemSpiel && $this->stichNr == 1 && $this->aktuellerStich->count() == 0) return;
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
        $reKarten = collect();
        $kontraPunkte = 0;
        $kontraKarten = collect();
        $schwarzGespielt = false;

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
            } else
            {
                $kontraPunkte += $spieler->punkte;

                foreach ($spieler->stiche as $stich)
                {
                    $kontraKarten = $kontraKarten->concat($stich->karten);
                }
            }
        }

        /*$rePunkte = 155;
        $reAnsage = true;
        $reAbsage = 90;
        $kontraPunkte = 240 - $rePunkte;
        $kontraAnsage = null;
        $kontraAbsage = null;*/

        $wertung = collect();
        $wertungsPunkte = 0;

        $this->reAugen = $rePunkte;
        $this->kontraAugen = $kontraPunkte;

        $punktegrenze = 120;
        if ($this->reAbsage !== null)
        {
            $punktegrenze = 240 - $this->reAbsage;
            $absage = $this->reAbsage;
        } elseif ($this->kontraAbsage !== null)
        {
            $punktegrenze = $this->kontraAbsage;
            $absage = $this->kontraAbsage;
        }


        if ($rePunkte == 240)
        {
            $schwarzGespielt = true;
            $rePunkte++;
        } elseif ($kontraPunkte == 240)
        {
            $schwarzGespielt = true;
            $kontraPunkte++;
        }

        $gewinntRe = $rePunkte > $punktegrenze ? true : false;

        /* **** Gewonnen **** */
        if ($rePunkte != 120) $wertungsPunkte++;
        if ($rePunkte != 120) $wertung->push(['Gewonnen', '+1']);

        /* **** Gegen die Alten **** */
        if ($this->spieltyp == 'Normalspiel' || $this->spieltyp == 'Hochzeit' || $this->spieltyp == 'Armut')
        {
            if ($gewinntRe == false) $wertungsPunkte++;
            if ($gewinntRe == false) $wertung->push(['Gegen die Alten', '+1']);
        }

        /* **** Re **** */
        if ($this->reAnsage) $wertungsPunkte += 2;
        if ($this->reAnsage) $wertung->push(['Re angesagt', '+2']);

        /* **** Kontra **** */
        if ($this->kontraAnsage) $wertungsPunkte += 2;
        if ($this->kontraAnsage) $wertung->push(['Kontra angesagt', '+2']);

        /* **** Absagen **** */
        if ($this->reAbsage !== null || $this->kontraAbsage !== null)
        {
            $absagePunkte = (120 - $absage) / 30;
            $wertungsPunkte += $absagePunkte;

            if ($this->reAbsage === 0 || $this->kontraAbsage === 0)
            {
                $wertung->push(['Schwarz angesasgt', '+' . $absagePunkte]);
            } else
            {
                $wertung->push(['Keine ' . $absage . ' angesagt', '+' . $absagePunkte]);
            }
        }

        /* **** Punkte für Erreichte Augen **** */
        $gewinnerAugen = $gewinntRe ? $rePunkte : $kontraPunkte;
        if ($gewinntRe && $this->kontraAbsage !== null || !$gewinntRe && $this->reAbsage !== null) // Verlorene Absage
        {
            $augenPunkte = (int)floor(($gewinnerAugen - $absage) / 30);
            if ($augenPunkte)
            {
                $wertung->push([30 * $augenPunkte . ' über Absage gespielt', '+' . $augenPunkte]);
            }
        } else
        { // Alle Anderen Fälle inklusive gewonnene Absage
            $augenPunkte = (int)floor(abs(($gewinnerAugen - 121) / 30));

            if ($schwarzGespielt)
            {
                $wertung->push(['Schwarz gespielt', '+' . $augenPunkte]);
            } elseif ($augenPunkte)
            {
                $wertung->push(['Keine ' . ceil((240 - $gewinnerAugen) / 30) * 30 . ' gespielt', '+' . $augenPunkte]);
            }
        }
        $wertungsPunkte += $augenPunkte;


        /* **** Extrapunkte **** */
        if (!$this->istSolo())
        {

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
                            $wertung->push(['Fuchs gefangen', '+1']);
                        } else
                        {
                            $wertungsPunkte--;
                            $wertung->push(['Fuchs gefangen', '-1']);
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
                            $wertung->push(['Fuchs gefangen', '-1']);
                        } else
                        {
                            $wertungsPunkte++;
                            $wertung->push(['Fuchs gefangen', '+1']);
                        }
                    }
                }
            }


            /* **** Karlchen **** */
            if ($this->schweinchen && $this->letzterStich->karten->where('rang', 14)->count() == 1)
            {
                $hoechsterRang = 14;
            } else
            {
                $hoechsterRang = $this->letzterStich->karten->max('rang');
            }
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

            if (!$this->liveRound->karlchen)
            {
                $reMachtKarlchen = false;
                $kontraMachtKarlchen = false;

                if (!$this->liveRound->karlchenFangen)
                {
                    $reFaengtKarlchen = 0;
                    $kontraFaengtKarlchen = 0;
                }
            }

            if ($gewinntRe)
            {
                if ($reMachtKarlchen)
                {
                    $wertungsPunkte++;
                    $wertung->push(['Karlchen', '+1']);
                }
                if ($kontraMachtKarlchen)
                {
                    $wertungsPunkte--;
                    $wertung->push(['Karlchen', '-1']);
                }
                if ($reFaengtKarlchen)
                {
                    $wertungsPunkte += $reFaengtKarlchen;
                    $wertung->push(['Karlchen gefangen', '+' . $reFaengtKarlchen]);
                }
                if ($kontraFaengtKarlchen)
                {
                    $wertungsPunkte -= $kontraFaengtKarlchen;
                    $wertung->push(['Karlchen gefangen', '-' . $kontraFaengtKarlchen]);
                }
            } else
            {
                if ($reMachtKarlchen)
                {
                    $wertungsPunkte--;
                    $wertung->push(['Karlchen', '-1']);
                }
                if ($kontraMachtKarlchen)
                {
                    $wertungsPunkte++;
                    $wertung->push(['Karlchen', '+1']);
                }
                if ($reFaengtKarlchen)
                {
                    $wertungsPunkte -= $reFaengtKarlchen;
                    $wertung->push(['Karlchen gefangen', '-' . $reFaengtKarlchen]);
                }
                if ($kontraFaengtKarlchen)
                {
                    $wertungsPunkte += $kontraFaengtKarlchen;
                    $wertung->push(['Karlchen gefangen', '+' . $kontraFaengtKarlchen]);
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
                        $wertung->push(['Doppelkopf', '+1']);
                    } else
                    {
                        $wertungsPunkte--;
                        $wertung->push(['Doppelkopf', '-1']);
                    }
                }
            }
        }

        $this->winners = $this->res->filter(function ($value, $key) use ($gewinntRe) {
            return $value === $gewinntRe;
        })->keys();

        $this->gewinntRe = $gewinntRe;
        $this->wertungsPunkte = $wertungsPunkte;
        $this->wertung = $wertung;
    }

    public function spielErgebnisUebertragen()
    {
        $round = $this->liveRound->round;
        $game = $round->addNewGame($this->winners->toArray(), $this->wertungsPunkte, false, $this->id);

        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($round->groups, $round->updated_at);
    }

    public function setVorbehalt($vorbehalt)
    {
        $spieler = $this->getSpieler();

        if ($vorbehalt == 'Gesund')
        {
            $spieler->vorbehalt = true;
            $this->setAnzeige('vorbehalt', $spieler->id, 'Gesund');
        } elseif ($vorbehalt == 'Stille Hochzeit')
        {
            $spieler->vorbehalt = $vorbehalt;
            $this->setAnzeige('vorbehalt', $spieler->id, 'Gesund');
        } else
        {
            $spieler->vorbehalt = $vorbehalt;
            $this->setAnzeige('vorbehalt', $spieler->id, 'Vorbehalt');
        }

        $this->spielerSpeichern($spieler);
    }

    public function getGegnerIDs($spielerID)
    {
        $spielerID = $spielerID ?? auth()->id();

        $ich = $this->getSpieler($spielerID);

        $reOderKontra = $ich->isRe;

        $gegner = $this->res->filter(function ($value, $key) use ($reOderKontra) {
            return $value != $reOderKontra;
        });

        return $gegner->keys();
    }

    public function habenMeineGegnerAngesagt($spieler)
    {
        return $spieler->isRe ? $this->kontraAnsage !== false : $this->reAnsage !== false;
    }

    public function habenMeineGegnerAbgesagt($spieler)
    {
        return $spieler->isRe ? $this->kontraAbsage !== null : $this->reAbsage !== null;
    }

    public function moeglicheAnAbsagenEintragen()
    {
        foreach ($this->spielerIDs as $key => $spielerID)
        {
            $spieler = $this->getSpieler($spielerID);

            $spieler->moeglicheAnAbsage = $this->moeglicheAnAbsageBerechnen($spieler);

            $this->spielerSpeichern($spieler);
        }
    }

    public function moeglicheAnAbsageBerechnen($spieler)
    {
        $ansageBisStich = $this->habenMeineGegnerAngesagt($spieler) ? 3 : 2;
        $ansage = $spieler->isRe ? $this->reAnsage : $this->kontraAnsage;
        $absage = $spieler->isRe ? $this->reAbsage : $this->kontraAbsage;

        if (!$ansage && $this->stichNr <= $ansageBisStich)
        {
            return $spieler->isRe ? 'Re' : 'Kontra';
        } elseif ($ansage &&                                // Hat schon angesagt
            $absage === null &&                       // Hat noch nicht abgesagt
            $this->stichNr <= $ansageBisStich + 1 &&                    // Darf nur bis zum dritten Stich absagen
            !$this->habenMeineGegnerAbgesagt($spieler))    // Gegner dürfen nicht abgesagt haben
        {
            return 90;
        } elseif ($ansage &&                                                                         // Hat schon angesagt
            $absage > 0 &&                                                                         // Hat schon abgesagt und noch nicht Schwarz gesagt
            $this->stichNr <= $ansageBisStich + 1 + (120 - $absage) / 30 &&                    // Darf nur bis zum dritten Stich absagen
            !$this->habenMeineGegnerAbgesagt($spieler))                                        // Gegner dürfen nicht abgesagt haben
        {
            $moeglicheAbsage = $absage - 30;
            $moeglicheAbsage = $moeglicheAbsage == 0 ? 'Schwarz' : strval($moeglicheAbsage);

            return $moeglicheAbsage;
        } else
        {
            return null;
        }
    }

    public function getAnsage($spieler)
    {
        return $spieler->isRe ? $this->reAnsage : $this->kontraAnsage;
    }

    public function getAbsage($spieler)
    {
        return $spieler->isRe ? $this->reAbsage : $this->kontraAbsage;
    }

    public function setAnsage($spieler)
    {
        if ($spieler->isRe) $this->reAnsage = true;
        else                $this->kontraAnsage = true;

        $spieler->ansage = true;
        $this->spielerSpeichern($spieler);
    }

    public function setAbsage($spieler, $absage)
    {
        if ($spieler->isRe) $this->reAbsage = $absage;
        else                $this->kontraAbsage = $absage;

        $spieler->absage = $absage;
        $this->spielerSpeichern($spieler);
    }

    public function ansageMachen($ansage)
    {
        $spieler = $this->getSpieler();

        abort_if($this->moeglicheAnAbsageBerechnen($spieler) != $ansage, 422, 'Du darfst diese Ansage nicht machen.');

        $this->setAnsage($spieler);

        $ansage = $spieler->isRe ? 'Re' : 'Kontra';
        if ($this->spieltyp != 'Hochzeit') $this->setAnzeige('partei', $spieler->id, $ansage . '-Partei');
        $this->setAnzeige('ansage', $spieler->id, $ansage);
        $this->pushMessage("<b>$spieler->name</b>: $ansage!");

        $this->wurdeOffengelegtDurchAnAbsage($spieler);
    }

    public function absageMachen($absage)
    {
        $absageZahl = $absage == 'Schwarz' ? 0 : intval($absage);
        $spieler = $this->getSpieler();

        abort_if($this->moeglicheAnAbsageBerechnen($spieler) != $absage, 422, 'Du darfst diese Absage nicht machen.');

        $this->setAbsage($spieler, $absageZahl);

        $ansage = $spieler->isRe ? 'Re' : 'Kontra';
        if ($this->spieltyp != 'Hochzeit') $this->setAnzeige('partei', $spieler->id, $ansage . '-Partei');

        $this->wurdeOffengelegtDurchAnAbsage($spieler);

        $this->setAnzeige('absage', $spieler->id, $absageZahl);

        $message = $absageZahl == 0 ? 'Schwarz!' : "Keine $absageZahl!";
        $this->pushMessage("<b>$spieler->name</b>: $message");
    }

    public function wurdeOffengelegtDurchAnAbsage($spieler)
    {
        if (($this->spieltyp == 'Normalspiel' && !$spieler->parteiOffengelegt) ||
            ($this->spieltyp == 'Stille Hochzeit' && !$spieler->isRe))
        {
            if ($spieler->isRe) $this->resOffengelegt++;
            else                $this->kontrasOffengelegt++;

            $spieler->parteiOffengelegt++;
            $this->spielerSpeichern($spieler);

            $this->parteienEindeutig();
        }
    }

    public function armutKartenAbgeben(Collection $karten)
    {
        $spieler = $this->getSpieler();

        $spieler->armutKarten = $karten;

        foreach ($karten as $karte)
        {
            $spieler->karteAusHandEntfernen($karte);
        }

        abort_if($spieler->hand->where('trumpf', true)->count() != 0, 422, 'Du musst all deinen Trumpf abgeben!');

        $this->spielerSpeichern($spieler);
    }

    public function armutKartenZurueckgebenUndReSetzen(Collection $karten)
    {
        $spieler = $this->getSpieler();

        foreach ($karten as $karte)
        {
            $spieler->karteAusHandEntfernen($karte);
        }

        $trumpf = 0;
        foreach ($karten as $karte)
        {
            if ($this->istTrumpf($karte)) $trumpf++;
        }

        $message = $trumpf == 0 ? 'Ohne Trumpf zurück!' : "Mit $trumpf Trumpf zurück!";
        $this->pushMessage($message);

        $armutSpieler = $this->getSpielerByPosition($this->vorbehalte->search('Armut'));
        $hand = $armutSpieler->hand->concat($karten);
        $armutSpieler->hand = $hand;

        $armutSpieler->isRe = true;
        $spieler->isRe = true;
        $this->setAnzeige('partei', $spieler->id, 'Re-Partei');

        $this->spielerSpeichern($armutSpieler);
        $this->spielerSpeichern($spieler);

        $this->alleSpielerKontraSetzen($spieler->id, true);
    }

    public function pushMessage($message, $name = null)
    {
        $messages = $this->messages;
        if ($name)
        {
            $message = "<b>$name:</b> $message";
        }

        $messages->prepend($message);
        $this->messages = $messages;
    }

    public function setAnzeige($attribut, $spielerID, $wert)
    {
        $anzeige = $this->anzeige;
        if ($spielerID == 0)
        {
            foreach ($this->spielerIDs as $ID)
            {
                $anzeige->set($attribut, $ID, $wert);
            }
        } else
        {
            $anzeige->set($attribut, $spielerID, $wert);
        }
        $this->anzeige = $anzeige;
    }
}