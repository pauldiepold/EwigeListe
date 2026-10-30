<?php

namespace App\Http\Resources;

use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Profile
 */
class EditUserProfileResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->group->name,
            'closed' => (bool) $this->group->closed,
            'default' => (bool) $this->default,
            'selectable' => $this->canBeDefault(),
        ];
    }
}
