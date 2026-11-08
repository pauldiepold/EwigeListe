<?php

namespace App\Events;

use App\Http\Resources\LiveGame as LiveGameResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveGameDataBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $liveGame;

    private $playerID;

    /**
     * Create a new event instance.
     */
    public function __construct($liveGame, $playerID)
    {
        $this->liveGame = $liveGame;
        $this->playerID = $playerID;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(
            'liveRound.'.$this->liveGame->live_round_id.'.'.$this->playerID
        );
    }

    public function broadcastWith()
    {
        return [
            'ich' => $this->liveGame->getSpieler($this->playerID),
            'liveGame' => new LiveGameResource($this->liveGame),
        ];
    }
}
