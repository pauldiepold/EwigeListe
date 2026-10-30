<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeRoundResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'players'    => $this->players->pluck('surname')->implode(' - '),
            'lastGameAt' => $this->games_max_created_at ? printDate(Carbon::parse($this->games_max_created_at)) : null,
        ];
    }
}
