<?php

namespace App\Http\Resources;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Group
 */
class GroupShowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{id: int, name: string, closed: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'closed' => (bool) $this->closed,
        ];
    }
}
