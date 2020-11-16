<?php

namespace App\Http\Resources;

use App\Http\Resources\Player as PlayerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Game extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'misplay' => $this->misplay,
            'solo' => $this->solo,
            'points' => $this->points,
            'dealer_index' => $this->dealerIndex,
            'created_at' => printDate($this->created_at),
            'created_by' => $this->createdBy,
            'players' => PlayerResource::collection($this->players),
            //'index' => $this->whenPivotLoaded('player_round', fn() => $this->pivot->index),
        ];
    }
}
