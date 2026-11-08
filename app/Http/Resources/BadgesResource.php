<?php

namespace App\Http\Resources;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Abzeichen, gruppiert nach Jahr und Monat (neueste zuerst), je Monat der Sieger nach Punkten und
 * nach Spielen. Erwartet sie bereits sortiert.
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
     * @return list<array{year: int, months: list<array{month: int, points: ?array{firstName: string, value: int, path: ?string}, games: ?array{firstName: string, value: int, path: ?string}}>}>
     */
    public function toArray(Request $request): array
    {
        return $this->resource
            ->groupBy('year')
            ->map(fn (Collection $badgesOfYear, int $year): array => [
                'year' => $year,
                'months' => $badgesOfYear
                    ->groupBy('month')
                    ->map(fn (Collection $badgesOfMonth, int $month): array => [
                        'month' => $month,
                        'points' => $this->winner($badgesOfMonth->firstWhere('type', 'points'), $request),
                        'games' => $this->winner($badgesOfMonth->firstWhere('type', 'games'), $request),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return ?array{firstName: string, value: int, path: ?string}
     */
    private function winner(?Badge $badge, Request $request): ?array
    {
        if ($badge === null) {
            return null;
        }

        return [
            'firstName' => $badge->player->first_name,
            'value' => $badge->value,
            'path' => $this->linked ? $badge->playerPathFor($request->user()) : null,
        ];
    }
}
