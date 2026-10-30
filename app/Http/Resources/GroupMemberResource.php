<?php

namespace App\Http\Resources;

use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Profile
 */
class GroupMemberResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->player_id,
            'name' => "{$this->player->surname} {$this->player->name}",
            'surname' => $this->player->surname,
            'path' => $this->path(),
            'avatarPath' => $this->player->avatar_path,
            'games' => (int) $this->games,
            'points' => (int) $this->points,
            'pointsPerGame' => (float) $this->pointsPerGame,
            'soli' => (int) $this->soli,
        ];
    }
}
