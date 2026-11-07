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
            // Alte Schlüssel für die Legacy-Komponenten hinter `fetchData`; fallen mit dem Live-Spiel (#36) weg.
            'surname' => $this->first_name,
            'name' => $this->last_name,
            'is_ai' => $this->is_ai,
            'won' => $this->whenPivotLoaded('game_player', fn () => $this->pivot->won),
            'points' => $this->whenPivotLoaded('game_player', fn () => $this->pivot->points),
        ];
    }
}
