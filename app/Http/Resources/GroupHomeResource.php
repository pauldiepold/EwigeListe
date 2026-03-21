<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupHomeResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        return [
            'id'      => $this->id,
            'records' => collect($this->records)->map(fn ($row) => [
                'labelHtml'  => (string) collect($row)->get(0, ''),
                'value'      => (string) collect($row)->get(1, ''),
                'detailHtml' => (string) collect($row)->get(2, ''),
            ])->values(),
            'stats' => collect($this->stats)->map(fn ($row) => [
                'labelHtml' => (string) collect($row)->get(0, ''),
                'value'     => (string) collect($row)->get(1, ''),
            ])->values(),
        ];
    }
}
