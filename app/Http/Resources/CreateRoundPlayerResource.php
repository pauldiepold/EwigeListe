<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Player
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
            'id'          => $this->id,
            'surname'     => $this->surname,
            'name'        => $this->name,
            'avatar_path' => $this->avatar_path,
            'groups'      => $this->whenLoaded('groups', function () {
                return $this->groups->map(function ($group) {
                    return [
                        'id'     => $group->id,
                        'name'   => $group->name,
                        'closed' => (int) $group->closed,
                    ];
                })->values();
            }),
            'profiles' => $this->whenLoaded('profiles', function () {
                return $this->profiles->map(function ($profile) {
                    return [
                        'group_id' => $profile->group_id,
                        'default'  => (bool) $profile->default,
                    ];
                })->values();
            }),
        ];
    }
}
