<?php

namespace App\Events;

use App\LiveGame;
use App\Http\Resources\LiveGame as LiveGameResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveGameDataBroadcastedInaktiv implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $liveGame;

    /**
     * Create a new event instance.
     *
     * @param $liveGame
     */
    public function __construct($liveGame)
    {
        $this->liveGame = $liveGame;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(
            'liveRound.' . $this->liveGame->live_round_id
        );
    }

    public function broadcastWith()
    {
        return [
            'liveGame' => new LiveGameResource($this->liveGame),
        ];
    }
}
