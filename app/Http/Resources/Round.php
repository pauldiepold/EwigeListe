<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Game as GameResource;
use App\Http\Resources\Player as PlayerResource;
use App\Http\Resources\PlayerFull as PlayerFullResource;
use App\Http\Resources\Group as GroupResource;

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
            'active_players' => $this->active_players,
            'inactive_players' => $this->inactive_players,
            'players_string' => $this->players_string,
            'path' => $this->players_string,
            'dealer_index' => $this->dealer_index,
            'created_at' => printDate($this->created_at),
            'created_by' => $this->createdBy,

            'games' => GameResource::collection($this->games),
            'players' => PlayerFullResource::collection($this->players),
            'groups' => GroupResource::collection($this->groups),
            'live_round' => $this->liveRound,
        ];
    }
}
