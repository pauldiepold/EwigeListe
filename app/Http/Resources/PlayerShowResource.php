<?php

namespace App\Http\Resources;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Player
 */
class PlayerShowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{id: int, fullName: string, avatarPath: ?string, editableUserId: ?int}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->full_name,
            'avatarPath' => $this->avatar_path,
            'editableUserId' => $request->user()->can('update', $this->user) ? $this->user->id : null,
        ];
    }
}
