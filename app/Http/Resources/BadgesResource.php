<?php

namespace App\Http\Resources;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Abzeichen, gruppiert nach Typ und Jahr (neueste zuerst). Erwartet sie bereits sortiert.
 *
 * @property Collection<int, Badge> $resource
 */
class BadgesResource extends JsonResource
{
    public static $wrap = null;

    private bool $linked = true;

    /**
     * Für das Profil: Die Karten würden sonst auf dieselbe Seite führen.
     */
    public function withoutLinks(): static
    {
        $this->linked = false;

        return $this;
    }

    /**
     * @return list<array{type: string, years: list<array{year: int, badges: list<array{name: string, value: int, date: string, path: ?string}>}>}>
     */
    public function toArray(Request $request): array
    {
        return $this->resource
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
                            'path' => $this->linked ? $badge->playerPathFor($request->user()) : null,
                        ])->all(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }
}
