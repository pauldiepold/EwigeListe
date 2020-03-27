<?php

namespace App\live;

class Card
{
    public $id;
    public $suit;
    public $rank;
    public $value;

    /**
     * Card constructor.
     * @param $id
     * @param $suit
     * @param $rank
     */
    public function __construct($id, $suit, $rank)
    {
        $this->id = $id;
        $this->suit = $suit;
        $this->rank = $rank;

        if ($rank == '9')
        {
            $this->value = 0;
        } elseif ($rank == '10')
        {
            $this->value = 10;
        } elseif ($rank == 'Bube')
        {
            $this->value = 2;
        } elseif ($rank == 'Dame')
        {
            $this->value = 3;
        } elseif ($rank == 'König')
        {
            $this->value = 4;
        } elseif ($rank == 'Ass')
        {
            $this->value = 11;
        }
    }
}