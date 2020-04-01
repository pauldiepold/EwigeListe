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
     * @param $hand
     * @param $index
     * @param $playerID
     * @param $playerName
     */
    public function __construct($playerID = '', $playerName = '', $index = '', $hand = '')
    {
        $this->player_id = $playerID;
        $this->player_name = $playerName;
        $this->index = $index;

        $this->hand = $hand;
        $this->stiche = collect();

        $this->isRe = false;
        $this->ansage = false;
        $this->absage = false;
    }

    public static function create($input)
    {
        $spieler = new self();

        $spieler->player_id = $input->player_id;
        $spieler->player_name = $input->player_name;
        $spieler->index = $input->index;

        $hand = collect($input->hand)->map(function ($item, $key) {
            return Karte::create(collect($item));
        });
        $spieler->hand = $hand;

        $stiche = collect($input->stiche)->map(function ($item, $key) {
            return Karte::create(collect($item));
        });
        $spieler->stiche = $stiche;

        $spieler->isRe = $input->isRe;
        $spieler->ansage = $input->ansage;
        $spieler->absage = $input->absage;

        return $spieler;
    }

    public function karteAusHandEntfernen($karte)
    {
        $hand = $this->hand->reject(function ($value, $key) use ($karte)
        {
            return $value == $karte;
        })->all();

        $this->hand = $hand;
    }

    public function stichNehmen($neuerStich) {
        dump($neuerStich);
        $stiche = $this->stiche->concat($neuerStich);
        dump($stiche);

        $this->stiche = $stiche;
    }

}