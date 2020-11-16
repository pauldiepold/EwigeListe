<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Player;
use App\Http\Resources\Group as GroupResource;

class PlayerFull extends JsonResource
{

    function __construct(Player $resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'name' => $this->name,
            'path' => $this->path,
            'avatar_path' => $this->avatar_path,
            'groups' => GroupResource::collection($this->groups),
            'index' => $this->whenPivotLoaded('player_round', fn() => $this->pivot->index),
        ];
    }
}
