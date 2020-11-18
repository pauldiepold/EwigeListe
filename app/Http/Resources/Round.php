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
            'active_players' => PlayerFullResource::collection($this->active_players),
            'inactive_players' => PlayerFullResource::collection($this->inactive_players),
            'players_string' => $this->players_string,
            'path' => $this->path,
            'dealer_index' => $this->dealer_index,
            'created_at_print' => printDate($this->created_at),
            'created_at' => $this->created_at,
            'created_by' => $this->createdBy,

            'online_players' => [],
            'authID' => auth()->id(),

            'games' => GameResource::collection($this->games),
            'players' => PlayerFullResource::collection($this->players),
            'groups' => GroupResource::collection($this->groups),
            'live_round' => $this->liveRound,
            'live_game' => $this->liveRound->currentLiveGame(),
            'ich' => $this->liveRound->currentLiveGame()->getSpieler(auth()->id()),
        ];
    }
}
