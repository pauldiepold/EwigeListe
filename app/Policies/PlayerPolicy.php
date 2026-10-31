<?php

namespace App\Policies;

use App\Player;
use App\User;

class PlayerPolicy
{
    /**
     * Der Admin kommt über `Gate::before` durch. Versteckte Spieler können sich nicht einloggen,
     * eine Ausnahme fürs eigene Profil ist daher nicht nötig. Gäste sind erlaubt, weil die Startseite
     * Rekorde auch ihnen zeigt; der Profil-Link führt sie zum Login.
     */
    public function view(?User $user, Player $player): bool
    {
        return ! $player->hide && ! $player->is_ai;
    }
}
