<?php

namespace App\Live;

class Tisch
{
    public $spieler0;
    public $spieler1;
    public $spieler2;
    public $spieler3;

    /**
     * LivePlayer constructor.
     * @param $spieler
     */
    public function __construct($spieler = null)
    {
        if (isset($spieler))
        {
            $this->spieler0 = $spieler->get(0);
            $this->spieler1 = $spieler->get(1);
            $this->spieler2 = $spieler->get(2);
            $this->spieler3 = $spieler->get(3);
        }
    }

    public static function create($input)
    {
        $tisch = new self();

        $tisch->spieler0 = Spieler::create($input->spieler0);
        $tisch->spieler1 = Spieler::create($input->spieler1);
        $tisch->spieler2 = Spieler::create($input->spieler2);
        $tisch->spieler3 = Spieler::create($input->spieler3);

        return $tisch;
    }

}