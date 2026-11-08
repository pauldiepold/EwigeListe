<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Player extends JsonResource
{
    /**
     * Liefert für die Legacy-Komponenten hinter `fetchData` noch die alten Schlüssel `surname` (Vorname)
     * und `name` (Nachname); bis zum Live-Spiel (#36).
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'surname' => $this->first_name,
            'name' => $this->last_name,
            'won' => $this->whenPivotLoaded('game_player', fn () => $this->pivot->won),
            'points' => $this->whenPivotLoaded('game_player', fn () => $this->pivot->points),
        ];
    }
}
