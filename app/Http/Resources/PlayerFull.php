<?php

namespace App\Http\Resources;

use App\Http\Resources\Group as GroupResource;
use App\Models\Player;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerFull extends JsonResource
{
    public function __construct(Player $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // Alte Schlüssel für die Legacy-Komponenten hinter `fetchData`; fallen mit dem Live-Spiel (#36) weg.
            'surname' => $this->first_name,
            'name' => $this->last_name,
            'path' => $this->path,
            'avatar_path' => $this->avatar_path,
            'online' => false,
            'is_ai' => $this->is_ai,
            'groups' => GroupResource::collection($this->groups),
            'index' => $this->whenPivotLoaded('player_round', fn () => $this->pivot->index),
        ];
    }
}
