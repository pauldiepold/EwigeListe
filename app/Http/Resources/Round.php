<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Game as GameResource;

class Round extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'active_players' => $this->active_players,
            'inactive_players' => $this->inactive_players,
            'players_string' => $this->players_string,
            'path' => $this->players_string,
            'dealer_index' => $this->dealer_index,

            'games' => GameResource::collection($this->games),
            'live_round' => $this->liveRound,
        ];
    }
}
