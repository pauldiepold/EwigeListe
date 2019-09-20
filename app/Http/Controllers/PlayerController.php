<?php

namespace App\Http\Controllers;

use App\Player;
use App\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{

    public function index($orderBy = 'games', $order = 'down')
    {
        $orderSQL = $order == 'up' ? 'asc' : 'desc';

        if ($orderBy == 'surname')
        {
            $orderTable = 'players';
            $orderSQL = $orderSQL == 'asc' ? 'desc' : 'asc';
        } else
        {
            $orderTable = 'profiles';
        }

        $players = Player::join('profiles', 'players.id', '=', 'profiles.player_id')
            ->orderBy($orderTable . '.' . $orderBy, $orderSQL)
            ->where('profiles.games', '>=', '10')
            ->where('players.hide', '0')
            ->with('profile')
            ->get();

        $playersCount = Player::where('players.hide', '0')->count();

        return view('players.index', compact('players', 'playersCount', 'orderBy', 'order'));
    }

    public function show(Player $player)
    {
        $rounds = $player->rounds()
            ->latest()
            ->with(['games', 'players'])
            ->paginate(15);

        $groups = $player->groups;

        return view('players.show', compact('player', 'rounds', 'groups'));

    }

}
