<?php

namespace App\Live;

use Illuminate\Support\Collection;

class Karte
{
    public $id;

    public $rang;

    public $farbe;

    public $wert;

    public $punkte;

    public $trumpf;
    public $spielbar;
    public $armut_zurueck;

    public $gespieltVon;

    public function __construct($id = 0, $farbe = 1, $wert = 1, $rang = 0)
    {
        $this->id = $id;
        $this->rang = floor($id / 2);
        $this->trumpf = false;
        $this->spielbar = false;
        $this->gespieltVon = false;
        $this->armut_zurueck = false;

        $this->farbe = $farbe;

        $this->wert = $wert;

        $this->wert = $wert;
        if ($wert == '1')
        {
            $this->punkte = 0;
        } elseif ($wert == '2')
        {
            $this->punkte = 2;
        } elseif ($wert == '3')
        {
            $this->punkte = 3;
        } elseif ($wert == '4')
        {
            $this->punkte = 4;
        } elseif ($wert == '5')
        {
            $this->punkte = 10;
        } elseif ($wert == '6')
        {
            $this->punkte = 11;
        }
    }

    public static function create($input)
    {
        $karte = new self();

        $karte->id = $input->id;
        $karte->rang = $input->rang;
        $karte->farbe = $input->farbe;
        $karte->wert = $input->wert;
        $karte->punkte = $input->punkte;
        $karte->trumpf = $input->trumpf;
        $karte->spielbar = $input->spielbar;
        $karte->gespieltVon = $input->gespieltVon;
        $karte->armut_zurueck = $input->armut_zurueck;

        return $karte;
    }
}