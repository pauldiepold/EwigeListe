<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Round
 */
class RoundArchiveRowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'path'            => route('rounds.show', ['round' => $this->id], false),
            'date'            => $this->updated_at->format('d.m.Y'),
            'games_count'     => $this->games_count,
            'has_live_round'  => (bool) $this->liveRound,
            'player_ids'      => $this->players->pluck('id')->values()->all(),
            'players_label'   => $this->players_string,
        ];
    }
}
