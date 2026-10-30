<?php

namespace App\Http\Resources;

use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Player
 */
class PlayerShowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{id: int, name: string, avatarPath: ?string, editableUserId: ?int}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => "{$this->surname} {$this->name}",
            'avatarPath' => $this->avatar_path,
            'editableUserId' => $request->user()->can('update', $this->user) ? $this->user->id : null,
        ];
    }
}
