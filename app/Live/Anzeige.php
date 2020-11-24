<?php


namespace App\Live;


class Anzeige
{
    public $spieler;

    public function __construct($players = '')
    {
        if ($players != '')
        {
            $spieler = collect();
            foreach ($players as $player)
            {
                $spieler->put($player->id, collect([
                    'ansage' => '',
                    'spieltyp' => '',
                    'partei' => '',
                    'vorbehalt' => '',
                ]));
            }
            $this->spieler = $spieler;
        }
    }

    public static function create($input)
    {
        if (isset($input))
        {
            $anzeige = new self();

            $spieler = collect($input->spieler)->map(function ($item, $key)
            {
                return collect($item);
            });
            $anzeige->spieler = $spieler;

            return $anzeige;
        } else
        {
            dd('fehler beim anzeige createn');

            return false;
        }
    }

    public function set($attribut, $spielerID, $wert)
    {
        if ($attribut == 'absage')
        {
            if ($wert == '0')
            {
                $wert = 'Schwarz';
            } else
            {
                $wert = 'keine ' . $wert;
            }
        }
        $attribut = $attribut == 'absage' ? 'ansage' : $attribut;

        $spieler = $this->spieler->get($spielerID);
        $spieler->put($attribut, $wert);
        $this->spieler->put($spielerID, $spieler);
    }
}