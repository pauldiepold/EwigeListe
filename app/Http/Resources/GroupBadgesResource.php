<?php

namespace App\Http\Resources;

use App\Badge;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Abzeichen einer Liste, gruppiert nach Typ und Jahr (neueste zuerst).
 *
 * @mixin Group
 */
class GroupBadgesResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return list<array<string, mixed>>
     */
    public function toArray(Request $request): array
    {
        return $this->badges
            ->groupBy('type')
            ->map(fn (Collection $badges, string $type): array => [
                'type' => $type,
                'years' => $badges
                    ->groupBy('year')
                    ->map(fn (Collection $badges, int $year): array => [
                        'year' => $year,
                        'badges' => $badges->map(fn (Badge $badge): array => [
                            'name' => $badge->player->surname,
                            'value' => $badge->value,
                            'date' => $badge->date->isoFormat('MMMM YYYY'),
                            'path' => $badge->playerPath(),
                        ])->all(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }
}
