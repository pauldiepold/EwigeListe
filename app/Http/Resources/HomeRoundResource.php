<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeRoundResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $lastGame = $this->games->last();

        return [
            'id'         => $this->id,
            'path'       => $this->path(),
            'players'    => $this->players->pluck('surname')->implode(' - '),
            'lastGameAt' => $lastGame ? printDate($lastGame->created_at) : null,
        ];
    }
}
