<?php


namespace App\live;


class Board
{
    public $deck;

    /**
     * Board constructor.
     */
    public function __construct()
    {
        $this->deck = [];
        $suits = ['Karo', 'Herz', 'Pik', 'Kreuz'];
        $ranks = ['9', '10', 'Bube', 'Dame', 'König', 'Ass'];
        $counter = 1;

        foreach ($ranks as $rank)
        {
            foreach ($suits as $suit)
            {
                for ($i = 0; $i <= 1; $i++)
                {
                    //dump(new Card($counter, $suit, $rank));
                    $this->deck[] = new Card($counter, $suit, $rank);
                    $counter++;
                }
            }
        }

        shuffle($this->deck);
    }

    /**
     * @return array
     */
    public function getDeck()
    {
        return $this->deck;
    }


}