<?php

namespace App\Http\Resources;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Player
 */
class PlayerShowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{id: int, fullName: string, avatarPath: ?string, avatarFullPath: ?string, editableUserId: ?int}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->full_name,
            'avatarPath' => $this->avatar_path,
            'avatarFullPath' => $this->avatarFullPath(),
            'editableUserId' => $request->user()->can('update', $this->user) ? $this->user->id : null,
        ];
    }

    /**
     * Neben dem verkleinerten Avatar speichert der Upload das Original als `*_full.jpg`, aber erst
     * seit November 2020. Ältere Avatare haben keins.
     */
    private function avatarFullPath(): ?string
    {
        $avatar = $this->user?->getRawOriginal('avatar_path');

        if ($avatar === null) {
            return null;
        }

        $fullAvatar = preg_replace('/\.jpg$/', '_full.jpg', $avatar);

        return $fullAvatar !== $avatar && Storage::disk('public')->exists('avatars/'.$fullAvatar)
            ? '/storage/avatars/'.$fullAvatar
            : null;
    }
}
