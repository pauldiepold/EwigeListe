<?php

namespace App\Live;

use Illuminate\Support\Collection;

class Karte
{
    public $id;

    public $farbe;
    public $farbName;

    public $wert;
    public $wertName;

    public $punkte;

    public $spielbar;

    public $gespieltVon;

    public function __construct($id = 0, $farbe = 1, $wert = 1)
    {
        $this->id = $id;
        $this->spielbar = true;
        $this->gespieltVon = false;

        $this->farbe = $farbe;

        if ($farbe == '1')
        {
            $this->farbName = '♦️';
        } elseif ($farbe == '2')
        {
            $this->farbName = '♥️';
        } elseif ($farbe == '3')
        {
            $this->farbName = '♠️';
        } elseif ($farbe == '4')
        {
            $this->farbName = '♣️';
        }

        $this->wert = $wert;
        if ($wert == '1')
        {
            $this->wertName = '9';
        } elseif ($wert == '2')
        {
            $this->wertName = '10';
        } elseif ($wert == '3')
        {
            $this->wertName = 'B';
        } elseif ($wert == '4')
        {
            $this->wertName = 'D';
        } elseif ($wert == '5')
        {
            $this->wertName = 'K';
        } elseif ($wert == '6')
        {
            $this->wertName = 'A';
        }

        $this->wert = $wert;
        if ($wert == '1')
        {
            $this->punkte = 0;
        } elseif ($wert == '2')
        {
            $this->punkte = 10;
        } elseif ($wert == '3')
        {
            $this->punkte = 2;
        } elseif ($wert == '4')
        {
            $this->punkte = 3;
        } elseif ($wert == '5')
        {
            $this->punkte = 4;
        } elseif ($wert == '6')
        {
            $this->punkte = 11;
        }
    }

    public static function create($input) {
        $karte = new self();

        $karte->id = $input->id;
        $karte->farbe = $input->farbe;
        $karte->farbName = $input->farbName;
        $karte->wert = $input->wert;
        $karte->wertName = $input->wertName;
        $karte->punkte = $input->punkte;
        $karte->spielbar = $input->spielbar;
        $karte->gespieltVon = $input->gespieltVon;

        return $karte;
    }
}