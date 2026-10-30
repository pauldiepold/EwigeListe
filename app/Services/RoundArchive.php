<?php

namespace App\Services;

use App\Group;
use App\Http\Resources\RoundArchiveRowResource;
use App\Round;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Sortierte, seitenweise Runden einer Liste für das Rundenarchiv.
 */
class RoundArchive
{
    private const SORT_COLUMNS = ['date', 'games', 'online'];

    /**
     * @return array{rounds: LengthAwarePaginator, archiveSort: array{column: string, direction: string}}
     */
    public function forGroup(Group $group, Request $request): array
    {
        $sort = in_array($request->query('sort'), self::SORT_COLUMNS, true) ? $request->query('sort') : 'date';
        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $roundsQuery = Round::whereHas('groups', function (Builder $query) use ($group) {
            $query->where('groups.id', '=', $group->id);
        })
            ->with(['players', 'liveRound'])
            ->withCount('games');

        match ($sort) {
            'games' => $roundsQuery
                ->orderBy('games_count', $direction)
                ->orderByDesc('rounds.id'),
            'online' => $roundsQuery
                ->orderByRaw('(live_round_id IS NOT NULL) '.strtoupper($direction))
                ->orderByDesc('rounds.id'),
            default => $roundsQuery
                ->orderBy('updated_at', $direction)
                ->orderByDesc('rounds.id'),
        };

        $paginator = $roundsQuery->paginate(20)->withQueryString();
        $paginator->setCollection(
            $paginator->getCollection()->map(
                static fn (Round $round) => (new RoundArchiveRowResource($round))->resolve($request)
            )
        );

        return [
            'rounds' => $paginator,
            'archiveSort' => [
                'column' => $sort,
                'direction' => $direction,
            ],
        ];
    }
}
