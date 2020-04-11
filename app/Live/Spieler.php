<?php


namespace App\Live;


class Spieler
{
    public $id;
    public $name;
    public $index;

    public $hand;
    public $stiche;

    public $gesund;
    public $moeglicheVorbehalte;
    public $vorbehalt;
    public $isRe;
    public $ansage;
    public $absage;
    public $punkte;

    /**
     * LivePlayer constructor.
     * @param $playerID
     * @param $hand
     * @param $playerIndex
     * @param $playerName
     */
    public function __construct($playerID = '', $playerName = '', $playerIndex = '', $hand = '')
    {
        $this->id = $playerID;
        $this->name = $playerName;
        $this->index = $playerIndex;

        $this->hand = $hand;
        $this->stiche = collect();

        $this->gesund = null;
        $this->moeglicheVorbehalte = collect();
        $this->vorbehalt = null;
        $this->isRe = null;
        $this->ansage = null;
        $this->absage = null;
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

            $hand = collect($input->hand)->map(function ($item, $key)
            {
                return Karte::create($item);
            });
            $spieler->hand = $hand;

            $stiche = collect($input->stiche)->map(function ($item, $key)
            {
                return Stich::create($item);
            });
            $spieler->stiche = $stiche;

            $spieler->gesund = $input->gesund;
            $spieler->moeglicheVorbehalte = collect($input->moeglicheVorbehalte);
            $spieler->vorbehalt = $input->vorbehalt;
            $spieler->isRe = $input->isRe;
            $spieler->ansage = $input->ansage;
            $spieler->absage = $input->absage;
            $spieler->punkte = $input->punkte;

            return $spieler;
        } else
        {
            return new self();
        }
    }

    public function karteAusHandEntfernen($karte)
    {
        $hand = $this->hand->reject(
            function ($value, $key) use ($karte)
            {
                return $value->id == $karte->id;
            }
        )->all();

        $this->hand = $hand;
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