<?php

namespace App\Events;

use App\LiveGame;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveGameDataBroadcasted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $liveGame;
    private $playerID;

    /**
     * Create a new event instance.
     *
     * @param $liveGame
     * @param $playerID
     */
    public function __construct($liveGame, $playerID)
    {
        $this->liveGame = $liveGame;
        $this->playerID = $playerID;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(
            'liveRound.' . $this->liveGame->live_round_id . '.' . $this->playerID
        );
    }

    public function broadcastWith()
    {
        return [
            'ich' => $this->liveGame->getSpieler($this->playerID),
            'liveGame' => $this->liveGame
        ];
    }
}
