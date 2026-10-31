<?php

namespace App\Services;

use App\Game;
use App\Player;
use App\Round;

class RoundChartSeries
{
    public const int MINIMUM_GAMES = 4;

    /**
     * Punkteverlauf pro Spieler, beginnend bei 0. Wer aussetzt, behält seinen Stand.
     *
     * @return array{names: list<string>, points: list<list<int>>}
     */
    public function forRound(Round $round): array
    {
        $series = $round->players
            ->mapWithKeys(fn (Player $player): array => [$player->id => [0]])
            ->all();

        $round->games->each(function (Game $game) use (&$series): void {
            foreach ($series as $playerId => $points) {
                $gamePlayer = $game->players->firstWhere('id', $playerId);
                $series[$playerId][] = end($points) + (int) ($gamePlayer?->pivot->points ?? 0);
            }
        });

        return [
            'names' => $round->players->pluck('surname')->all(),
            'points' => array_values($series),
        ];
    }
}
