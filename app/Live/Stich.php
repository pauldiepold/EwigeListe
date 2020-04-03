<?php

namespace App\Live;

class Stich
{
    public $karten;

    public $stecher;

    public function __construct()
    {
        $this->karten = collect();
        $this->stecher = null;
    }

    public static function create($input)
    {
        if (isset($input))
        {
            $stich = new self();
            //dd($input);
            $stich->stecher = $input->stecher;

            $karten = collect($input->karten)->map(function ($item, $key)
            {
                return Karte::create($item);
            });
            $stich->karten = $karten;

            return $stich;
        } else
        {
            return new self();
        }
    }

    public function push(Karte $karte)
    {
        $this->karten->push($karte);
    }

    public function count()
    {
        return $this->karten->count();
    }
}