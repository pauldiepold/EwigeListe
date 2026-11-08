<?php

namespace App\Http\Resources;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Player
 */
class CreateRoundPlayerResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->full_name,
            'avatarPath' => $this->avatar_path,
            'groups' => $this->whenLoaded('groups', function () {
                return $this->groups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'closed' => (bool) $group->closed,
                    ];
                })->values();
            }),
            'profiles' => $this->whenLoaded('profiles', function () {
                return $this->profiles->map(function ($profile) {
                    return [
                        'groupId' => $profile->group_id,
                        'default' => (bool) $profile->default,
                    ];
                })->values();
            }),
        ];
    }
}
