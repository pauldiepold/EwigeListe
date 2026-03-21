<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Group
 */
class GroupIndexResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'closed'        => (bool) $this->closed,
            'players_count' => (int) $this->players_count,
            'rounds_count'  => (int) $this->rounds_count,
        ];
    }
}
