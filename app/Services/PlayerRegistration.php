<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Player;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Der einzige Weg zu einem neuen Spieler: Spieler, Benutzer und Mitgliedschaft in der Ewigen Liste
 * entstehen gemeinsam oder gar nicht.
 */
class PlayerRegistration
{
    public function register(string $surname, string $name, string $email, string $password): User
    {
        return DB::transaction(function () use ($surname, $name, $email, $password): User {
            $player = Player::create([
                'surname' => $surname,
                'name' => $name,
            ]);

            $user = User::create([
                'player_id' => $player->id,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            Group::find(Group::EWIGE_LISTE_ID)->addPlayer($player);

            $player->profiles->first()->calculate();

            return $user;
        });
    }
}
