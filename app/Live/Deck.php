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

        $this->deck->push(new Karte(47, 2, 5)); // Herz 10
        $this->deck->push(new Karte(46, 2, 5)); // Herz 10
        $this->deck->push(new Karte(45, 4, 3)); // Damen
        $this->deck->push(new Karte(44, 4, 3)); // Damen
        $this->deck->push(new Karte(43, 3, 3)); // Damen
        $this->deck->push(new Karte(42, 3, 3)); // Damen
        $this->deck->push(new Karte(41, 2, 3)); // Damen
        $this->deck->push(new Karte(40, 2, 3)); // Damen
        $this->deck->push(new Karte(39, 1, 3)); // Damen
        $this->deck->push(new Karte(38, 1, 3)); // Damen
        $this->deck->push(new Karte(37, 4, 2)); // Buben
        $this->deck->push(new Karte(36, 4, 2)); // Buben
        $this->deck->push(new Karte(35, 3, 2)); // Buben
        $this->deck->push(new Karte(34, 3, 2)); // Buben
        $this->deck->push(new Karte(33, 2, 2)); // Buben
        $this->deck->push(new Karte(32, 2, 2)); // Buben
        $this->deck->push(new Karte(31, 1, 2)); // Buben
        $this->deck->push(new Karte(30, 1, 2)); // Buben
        $this->deck->push(new Karte(29, 1, 6)); // Fuchs
        $this->deck->push(new Karte(28, 1, 6)); // Fuchs
        $this->deck->push(new Karte(27, 1, 5)); // Karo 10
        $this->deck->push(new Karte(26, 1, 5)); // Karo 10
        $this->deck->push(new Karte(25, 1, 4)); // Karo König
        $this->deck->push(new Karte(24, 1, 4)); // Karo König
        $this->deck->push(new Karte(23, 1, 1)); // Karo 9
        $this->deck->push(new Karte(22, 1, 1)); // Karo 9
        $this->deck->push(new Karte(21, 2, 6)); // Herz Ass
        $this->deck->push(new Karte(20, 2, 6)); // Herz Ass
        $this->deck->push(new Karte(19, 2, 4)); // Herz König
        $this->deck->push(new Karte(18, 2, 4)); // Herz König
        $this->deck->push(new Karte(17, 2, 1)); // Herz 9
        $this->deck->push(new Karte(16, 2, 1)); // Herz 9
        $this->deck->push(new Karte(15, 4, 6)); // Kreuz
        $this->deck->push(new Karte(14, 4, 6)); // Kreuz
        $this->deck->push(new Karte(13, 4, 5)); // Kreuz
        $this->deck->push(new Karte(12, 4, 5)); // Kreuz
        $this->deck->push(new Karte(11, 4, 4)); // Kreuz
        $this->deck->push(new Karte(10, 4, 4)); // Kreuz
        $this->deck->push(new Karte(9, 4, 1)); // Kreuz
        $this->deck->push(new Karte(8, 4, 1)); // Kreuz
        $this->deck->push(new Karte(7, 3, 6)); // Pik
        $this->deck->push(new Karte(6, 3, 6)); // Pik
        $this->deck->push(new Karte(5, 3, 5)); // Pik
        $this->deck->push(new Karte(4, 3, 5)); // Pik
        $this->deck->push(new Karte(3, 3, 4)); // Pik
        $this->deck->push(new Karte(2, 3, 4)); // Pik
        $this->deck->push(new Karte(1, 3, 1)); // Pik
        $this->deck->push(new Karte(0, 3, 1)); // Pik

        $this->deck = $this->deck->shuffle();
        $this->deck = $this->deck->split(4);
    }

    public function getKarten ($i) {
        return $this->deck->get($i);
    }


}