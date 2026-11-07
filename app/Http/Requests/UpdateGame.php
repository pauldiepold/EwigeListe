<?php

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\Round;
use Illuminate\Support\Collection;

class UpdateGame extends StoreGame
{
    /**
     * @return Collection<int, int>
     */
    protected function activePlayerIds(): Collection
    {
        return $this->round()->getActivePlayersForDealer($this->round()->getDealerIndexOf($this->game()))->pluck('id');
    }

    protected function round(): Round
    {
        return $this->game()->round;
    }

    private function game(): Game
    {
        return $this->route('game');
    }
}
