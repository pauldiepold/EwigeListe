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
            'first_player' => new PlayerResource($this->active_players->first()),

            'online_players' => [],
            'ready_players' => [],
            'watching_players' => [],
            'auth_id' => auth()->id(),

            'games' => GameResource::collection($this->games),
            'players' => PlayerFullResource::collection($this->players),
            'groups' => GroupResource::collection($this->groups),
            'live_round' => new LiveRound($this->liveRound),
            'current_live_game' => $this->when($this->liveRound && $this->liveRound->currentLiveGame(), fn() => new LiveGame($this->liveRound->currentLiveGame())),
            'last_live_game' => $this->when($this->liveRound && $this->liveRound->lastLiveGame(), fn() => new LiveGame($this->liveRound->lastLiveGame())),
            'ich' => $this->when($this->active_players->pluck('id')->contains(auth()->id()) && $this->liveRound && $this->liveRound->currentLiveGame(), fn() => $this->liveRound->currentLiveGame()->getSpieler(auth()->id(), false)),
        ];
    }
}
