<?php

namespace App\Http\Resources;

use App\Group;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Group
 */
class GroupStatisticsResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'records' => $this->recordRows()->map(fn (array $record): array => [
                'label' => $record['label'],
                'value' => $record['value'],
                'holders' => $record['profiles']->map(fn (Profile $profile): array => [
                    'name' => $profile->player->surname,
                    'path' => $profile->path(),
                ])->all(),
            ])->all(),
            'stats' => $this->statisticRows(),
        ];
    }
}
