<?php


namespace App\Live;


class Deck
{
    public $deck;

    /**
     * Deck constructor.
     */
    public function __construct()
    {
        $this->deck = collect();
        $farben = ['1', '2', '3', '4'];
        $werte = ['1', '2', '3', '4', '5', '6'];
        $counter = 1;

        foreach ($werte as $wert)
        {
            foreach ($farben as $farbe)
            {
                for ($i = 0; $i <= 1; $i++)
                {
                    $this->deck->push(new Karte($counter, $farbe, $wert));
                    $counter++;
                }
            }
        }

        $this->deck = $this->deck->shuffle();
        $this->deck = $this->deck->split(4);
    }
}