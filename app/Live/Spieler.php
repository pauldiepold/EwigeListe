<?php


namespace App\Live;


class Spieler
{
    public $id;
    public $name;
    public $avatar;
    public $index;

    public $hand;
    public $stiche;
    public $armutKarten;

    public $moeglicheVorbehalte;
    public $vorbehalt;
    public $isRe;
    public $ansage;
    public $absage;
    public $moeglicheAnAbsage;
    public $punkte;

    /**
     * LivePlayer constructor.
     * @param $playerID
     * @param $playerName
     * @param $playerIndex
     * @param $avatar
     */
    public function __construct($playerID = '', $playerName = '', $playerIndex = '', $avatar = '')
    {
        $this->id = $playerID;
        $this->name = $playerName;
        $this->avatar = $avatar;
        $this->index = $playerIndex;

        $this->hand = collect();
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
            $spieler->avatar = $input->avatar;
            $spieler->index = $input->index;

            if ($input->hand != '')
            {

                $hand = collect($input->hand)->map(function ($item, $key)
                {
                    return Karte::create($item);
                });
                $spieler->hand = $hand;
            } else
            {
                $spieler->hand = $input->hand;
            }

            $stiche = collect($input->stiche)->map(function ($item, $key)
            {
                return Stich::create($item);
            });
            $spieler->stiche = $stiche;

            if ($input->armutKarten != '')
            {

                $armutKarten = collect($input->armutKarten)->map(function ($item, $key)
                {
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
        $index = $this->hand->search(function ($item, $key) use ($karte)
        {
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
}