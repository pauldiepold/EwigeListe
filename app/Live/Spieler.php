<?php


namespace App\Live;


class Spieler
{
    public $player_id;
    public $player_name;
    public $index;
    public $hand;
    public $stiche;
    public $isRe;
    public $ansage;
    public $absage;

    /**
     * LivePlayer constructor.
     * @param $playerID
     * @param $hand
     * @param $playerIndex
     * @param $playerName
     */
    public function __construct($playerID = '', $playerName = '', $playerIndex = '', $hand = '')
    {
        $this->player_id = $playerID;
        $this->player_name = $playerName;
        $this->index = $playerIndex;

        $this->hand = $hand;
        $this->stiche = collect();

        $this->isRe = false;
        $this->ansage = false;
        $this->absage = false;
    }

    public static function create($input)
    {
        if (isset($input))
        {
            $spieler = new self();

            $spieler->player_id = $input->player_id;
            $spieler->player_name = $input->player_name;
            $spieler->index = $input->index;

            $hand = collect($input->hand)->map(function ($item, $key)
            {
                return Karte::create($item);
            });
            $spieler->hand = $hand;
            //dd($input->stiche);
            $stiche = collect($input->stiche)->map(function ($item, $key)
            {
                return Stich::create($item);
            });
            $spieler->stiche = $stiche;

            $spieler->isRe = $input->isRe;
            $spieler->ansage = $input->ansage;
            $spieler->absage = $input->absage;

            return $spieler;
        } else
        {
            return new self();
        }
    }

    public function karteAusHandEntfernen($karte)
    {
        $hand = $this->hand->reject(function ($value, $key) use ($karte)
        {
            return $value == $karte;
        })->all();

        $this->hand = $hand;
    }

    public function stichNehmen($stich)
    {
        $stiche = $this->stiche;
        $stiche = $stiche->push($stich);
        $this->stiche = $stiche;
    }

}