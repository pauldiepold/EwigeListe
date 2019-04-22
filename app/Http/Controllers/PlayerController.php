<?php

namespace App\Http\Controllers;

use App\Player;
use App\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller {

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
            ->with('profile')
            ->get();

        return view('players.index', compact('players', 'orderBy', 'order'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Player $player)
    {

    }

    public function edit(Player $player)
    {
        //
    }

    public function update(Request $request, Player $player)
    {
        //
    }

    public function destroy(Player $player)
    {
        //
    }
}
