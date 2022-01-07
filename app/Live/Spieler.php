<?php


namespace App\Live;


class Spieler
{
    public $id;
    public $name;
    public $index;
    public $ai;

    public $hand;
    public $hand_save;
    public $stiche;
    public $armutKarten;

    public $moeglicheVorbehalte;
    public $vorbehalt;
    public $isRe;
    public $ansage;
    public $absage;
    public $moeglicheAnAbsage;
    public $punkte;

    public $parteiOffengelegt;

    /**
     * LivePlayer constructor.
     * @param $playerID
     * @param $playerName
     * @param $playerIndex
     */
    public function __construct($player = null)
    {
        if ($player)
        {
            $this->id = $player->id;
            $this->name = $player->surname . ' ' . $player->name;
            $this->index = $player->pivot->index;
            $this->ai = $player->is_ai ? new AI($player) : null;
        } else
        {
            $this->id = '';
            $this->name = '';
            $this->index = '';
            $this->ai = null;
        }
        $this->parteiOffengelegt = 0;

        $this->hand = collect();
        $this->hand_save = collect();
        $this->stiche = collect();
        $this->armutKarten = collect();

        $this->moeglicheVorbehalte = collect();
        $this->vorbehalt = null;
        $this->isRe = null;
        $this->ansage = null;
        $this->absage = null;
        $this->moeglicheAnAbsage = null;
        $this->punkte = null;
    }

    public static function create($input)
    {
        if (isset($input))
        {
            $spieler = new self();

            $spieler->id = $input->id;
            $spieler->name = $input->name;
            $spieler->index = $input->index;

            $spieler->ai = $input->ai ? AI::create(collect($input->ai)) : null;

            $spieler->parteiOffengelegt = $input->parteiOffengelegt;

            if ($input->hand != '')
            {

                $hand = collect($input->hand)->map(function ($item, $key) {
                    return Karte::create($item);
                });
                $spieler->hand = $hand;
            } else
            {
                $spieler->hand = $input->hand;
            }

            if ($input->hand_save != '')
            {

                $hand_save = collect($input->hand_save)->map(function ($item, $key) {
                    return Karte::create($item);
                });
                $spieler->hand_save = $hand_save;
            } else
            {
                $spieler->hand_save = $input->hand_save;
            }

            $stiche = collect($input->stiche)->map(function ($item, $key) {
                return Stich::create($item);
            });
            $spieler->stiche = $stiche;

            if ($input->armutKarten != '')
            {

                $armutKarten = collect($input->armutKarten)->map(function ($item, $key) {
                    return Karte::create($item);
                });
                $spieler->armutKarten = $armutKarten;
            } else
            {
                $spieler->armutKarten = $input->armutKarten;
            }

            $spieler->moeglicheVorbehalte = collect($input->moeglicheVorbehalte);
            $spieler->vorbehalt = $input->vorbehalt;
            $spieler->isRe = $input->isRe;
            $spieler->ansage = $input->ansage;
            $spieler->absage = $input->absage;
            $spieler->moeglicheAnAbsage = $input->moeglicheAnAbsage;
            $spieler->punkte = $input->punkte;

            return $spieler;
        } else
        {
            return new self();
        }
    }

    public function karteAusHandEntfernen($karte)
    {
        $index = $this->hand->search(function ($item, $key) use ($karte) {
            return $item->id == $karte->id;
        });

        $this->hand->splice($index, 1);
    }

    public function stichNehmen($stich)
    {
        $stiche = $this->stiche;
        $stiche = $stiche->push($stich);
        $this->stiche = $stiche;
    }

    public function punkteZaehlen()
    {
        $punkte = 0;
        foreach ($this->stiche as $stich)
        {
            $punkte += $stich->punkteZaehlen();
        }
        $this->punkte = $punkte;
    }

    public function getAIString()
    {
        return $this->hand->map(fn($card) => $card->getAIString());
    }
}