<?php

namespace App\Http\Controllers;

use App\Player;
use App\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller {

    public function index()
    {
        $players = Player::all();

        return view('players.index', ['players' => $players]);
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
        $profile = $player->profile;

        $rounds = $player->rounds()->orderBy('created_at', 'asc')->paginate(10);

        return view('players.show', compact('player', 'profile', 'rounds'));
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
