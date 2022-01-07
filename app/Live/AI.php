<?php

namespace App\Live;

class AI
{
    public $ai_path;

    public function __construct($player = null)
    {
        $this->ai_path = $player ? $player->ai_path : '';
    }

    public static function create($input)
    {
        if (isset($input))
        {
            $ai = new self();

            $ai->ai_path = $input->get('ai_path');

            return $ai;
        } else
        {
            dd('fehler beim ai createn');

            return false;
        }
    }

    public function getBestCard($liveGame)
    {
        $spieler = $liveGame->getSpieler($liveGame->dran);

        $computer_player_id = intval(substr($liveGame->getSpielerString($liveGame->dran), -1));
        $starting_player_id = 0;

        $stichKarten = $liveGame->stiche->map(fn($stich) => $stich->getAIString())->flatten();
        $aktuellerStichKarten = $liveGame->aktuellerStich->getAIString();
        $played_cards = $stichKarten->concat($aktuellerStichKarten);
        $computer_player_hand = $spieler->getAIString();

        $json = '\'' . json_encode(compact('computer_player_id', 'starting_player_id', 'played_cards', 'computer_player_hand')) . '\'';
        // '\'{"computer_player_id": 0, "starting_player_id": 0, "played_cards": [], "computer_player_hand": ["cn", "cn", "ca", "sn", "st", "sq", "sj", "sj", "ht", "ht", "ha", "hn"]}\'';

        $path = dirname(__FILE__);
        $bestCard = shell_exec('python3 ' . $path . '/ai/frontend.py -l ' . $path . '/ai/lib_doko.so -j ' . $json);
        $bestCard = json_decode($bestCard)->best_card;
        $bestCardID = $spieler->hand->filter(fn($karte) => $karte->matchesAIString($bestCard))->first()->id;
        $bestCard = $liveGame->getKarteVonSpieler($bestCardID, $spieler->id);

        return $bestCard;
    }
}