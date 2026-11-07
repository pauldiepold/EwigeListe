<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Player extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'name' => $this->name,
            'is_ai' => $this->is_ai,
            'won' => $this->whenPivotLoaded('game_player', fn () => $this->pivot->won),
            'points' => $this->whenPivotLoaded('game_player', fn () => $this->pivot->points),
        ];
    }
}
