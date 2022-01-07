<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Group as GroupResource;

class Player extends JsonResource
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
            'surname' => $this->surname,
            'name' => $this->name,
            'is_ai' => $this->is_ai,
            'won' => $this->whenPivotLoaded('game_player', fn() => $this->pivot->won),
            'points' => $this->whenPivotLoaded('game_player', fn() => $this->pivot->points),
        ];
    }
}
