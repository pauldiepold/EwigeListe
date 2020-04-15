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
                    'surname' => $player->surname,
                    'name' => $player->name,
                    'id' => $player->id,
                    'avatar' => $player->avatar_path,
                    'punkte' => rand(-25, 25),
                    'ansage' => false,
                    'absage' => false,
                    'kreuzDameGespielt' => false,
                    'solo' => false,
                    'hochzeit' => false,
                    'armut' => false,
                    'online' => true,
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

    public function set($attribut, $spielerID, $ansage)
    {
        if ($attribut == 'absage')
        {
            if ($ansage == '0')
            {
                $ansage = 'Schwarz';
            } else
            {
                $ansage = 'Keine ' . $ansage;
            }
        }

        $spieler = $this->spieler->get($spielerID);
        $spieler->put($attribut, $ansage);
        $this->spieler->put($spielerID, $spieler);
    }
}