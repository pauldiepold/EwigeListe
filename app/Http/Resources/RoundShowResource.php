<?php

namespace App\Http\Resources;

use App\Game;
use App\Group;
use App\Player;
use App\Round;
use App\Services\RoundChartSeries;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @mixin Round
 */
class RoundShowResource extends JsonResource
{
    public static $wrap = null;

    private const int MINIMUM_GAMES_FOR_DURATION = 4;

    private const int RUNNING_ROUND_SECONDS = 30 * 60;

    private const int MINIMUM_PLAUSIBLE_GAME_SECONDS = 2 * 60;

    private const int MAXIMUM_PLAUSIBLE_GAME_SECONDS = 40 * 60;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        $dealerIndex = $this->getDealerIndex();
        $activePlayerIds = $this->getActivePlayers()->pluck('id');

        return [
            'id' => $this->id,
            'isLive' => $this->isLive(),
            'canUpdate' => $viewer->can('update', $this->resource),
            'viewerIsActive' => $activePlayerIds->contains($viewer->player_id),
            'hasChart' => $this->games->count() >= RoundChartSeries::MINIMUM_GAMES,
            'lastGameId' => $this->games->last()?->id,
            'players' => $this->players->map(fn (Player $player): array => [
                'id' => $player->id,
                'surname' => $player->surname,
                'path' => $player->pathFor($viewer),
                'avatarPath' => $player->avatar_path,
                'isDealer' => (int) $player->pivot->index === $dealerIndex,
                'isActive' => $activePlayerIds->contains($player->id),
            ])->all(),
            'activePlayers' => $this->getActivePlayersRotatedForUser()->map(fn (Player $player): array => [
                'id' => $player->id,
                'name' => "{$player->surname} {$player->name}",
                'avatarPath' => $player->avatar_path,
            ])->values()->all(),
            'games' => $this->gameRows(),
            'info' => $this->info(),
            'lists' => $this->lists(),
        ];
    }

    /**
     * @return list<array{id: int, points: int, isSolo: bool, isMisplay: bool, endsDealerCycle: bool, scores: list<array{points: ?int, won: bool}>}>
     */
    private function gameRows(): array
    {
        $playerCount = $this->players->count();
        $totals = $this->players->mapWithKeys(fn (Player $player): array => [$player->id => 0])->all();

        $gamesMovingTheDealer = 0;

        return $this->games->map(function (Game $game) use ($playerCount, &$totals, &$gamesMovingTheDealer): array {
            $scores = $this->players->map(function (Player $player) use ($game, &$totals): array {
                $gamePlayer = $game->players->firstWhere('id', $player->id);

                if ($gamePlayer === null) {
                    return ['points' => null, 'won' => false];
                }

                $totals[$player->id] += (int) $gamePlayer->pivot->points;

                return ['points' => $totals[$player->id], 'won' => (bool) $gamePlayer->pivot->won];
            })->all();

            $isSolo = (bool) $game->solo;
            $isMisplay = (bool) $game->misplay;
            $movesTheDealer = ! $isSolo && ! $isMisplay;
            $gamesMovingTheDealer += $movesTheDealer ? 1 : 0;

            return [
                'id' => $game->id,
                'points' => (int) $game->points,
                'isSolo' => $isSolo,
                'isMisplay' => $isMisplay,
                'endsDealerCycle' => $movesTheDealer && $gamesMovingTheDealer % $playerCount === 0,
                'scores' => $scores,
            ];
        })->all();
    }

    /**
     * @return array{gamesCount: int, averageGameDuration: ?string, startedBy: string, startedAt: string, lastGameBy: ?string, lastGameAt: ?string}
     */
    private function info(): array
    {
        $lastGame = $this->games->last();

        return [
            'gamesCount' => $this->games->count(),
            'averageGameDuration' => $this->averageGameDuration(),
            'startedBy' => $this->createdBy->surname,
            'startedAt' => printDate($this->created_at),
            'lastGameBy' => $lastGame?->createdBy?->surname,
            'lastGameAt' => $lastGame ? printDate($lastGame->created_at) : null,
        ];
    }

    /**
     * Solange die Runde läuft, zählt die Zeit bis jetzt mit. Unplausible Werte (Runde über mehrere
     * Tage, nachträglich eingetragene Spiele) werden nicht gezeigt.
     */
    private function averageGameDuration(): ?string
    {
        $gamesCount = $this->games->count();

        if ($gamesCount < self::MINIMUM_GAMES_FOR_DURATION) {
            return null;
        }

        $firstGameAt = $this->games->first()->created_at;
        $lastGameAt = $this->games->last()->created_at;
        $now = Carbon::now();

        $seconds = $lastGameAt->diffInSeconds($now) < self::RUNNING_ROUND_SECONDS
            ? $firstGameAt->diffInSeconds($now) / $gamesCount
            : $firstGameAt->diffInSeconds($lastGameAt) / ($gamesCount - 1);

        if ($seconds <= self::MINIMUM_PLAUSIBLE_GAME_SECONDS || $seconds >= self::MAXIMUM_PLAUSIBLE_GAME_SECONDS) {
            return null;
        }

        $roundedSeconds = (int) round($seconds);

        return intdiv($roundedSeconds, 60).' Minuten und '.($roundedSeconds % 60).' Sekunden';
    }

    /**
     * Alle Listen der Runde, auch geschlossene, dazu die offenen Listen ihrer Spieler als Auswahl.
     *
     * @return list<array{id: int, name: string, selected: bool, locked: bool}>
     */
    private function lists(): array
    {
        $selectedIds = $this->groups->pluck('id');

        return $this->players
            ->flatMap(fn (Player $player): Collection => $player->groups)
            ->merge($this->groups)
            ->unique('id')
            ->filter(fn (Group $group): bool => ! $group->closed || $selectedIds->contains($group->id))
            ->sortBy([
                fn (Group $a, Group $b): int => ($b->id === Group::EWIGE_LISTE_ID) <=> ($a->id === Group::EWIGE_LISTE_ID),
                fn (Group $a, Group $b): int => strcasecmp($a->name, $b->name),
            ])
            ->map(fn (Group $group): array => [
                'id' => $group->id,
                'name' => $group->name,
                'selected' => $selectedIds->contains($group->id),
                'locked' => $group->id === Group::EWIGE_LISTE_ID || (bool) $group->closed,
            ])
            ->values()
            ->all();
    }
}
