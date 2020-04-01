<?php

namespace App\Events;

use App\LiveGame;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveGameSaved
{
    use Dispatchable, SerializesModels;

    public $liveGame;

    /**
     * Create a new event instance.
     *
     * @param LiveGame $liveGame
     */
    public function __construct(LiveGame $liveGame)
    {
        $this->liveGame = $liveGame;
    }
}
