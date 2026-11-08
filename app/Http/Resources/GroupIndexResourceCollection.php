<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Ohne äußeres `data`-Wrapper-Objekt für Inertia-Props (reines Array).
 *
 * @extends ResourceCollection<int, GroupIndexResource>
 */
class GroupIndexResourceCollection extends ResourceCollection
{
    public static $wrap = null;

    public $collects = GroupIndexResource::class;

    /**
     * @return list<array<string, mixed>>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return array_values(parent::toArray($request));
    }
}
