<?php

namespace App\Http\Controllers;

use App\Events\RoundUpdated;
use App\Http\Requests\StoreGame;
use App\Http\Requests\UpdateGame;
use App\Http\Toast;
use App\Models\Game;
use App\Models\Group;
use App\Models\Profile;
use App\Models\Round;
use Illuminate\Http\RedirectResponse;

class GameController extends Controller
{
    private const string LIVE_ROUND_MESSAGE = 'In einer Live-Runde werden Spiele automatisch eingetragen.';

    public function store(StoreGame $request, Round $round): RedirectResponse
    {
        if ($round->isLive()) {
            Toast::error(self::LIVE_ROUND_MESSAGE);

            return back();
        }

        $validated = $request->validated();

        $round->addNewGame($validated['winners'], $validated['points'], $validated['misplayed'] ?? false);

        $this->announceChange($round);

        return redirect()->route('rounds.show', $round);
    }

    public function update(UpdateGame $request, Game $game): RedirectResponse
    {
        $round = $game->round;

        if ($response = $this->rejectUnlessLastGameOfOfflineRound($game, 'bearbeiten')) {
            return $response;
        }

        $validated = $request->validated();

        $round->updateGame($game, $validated['winners'], $validated['points'], $validated['misplayed'] ?? false);

        $this->announceChange($round);

        return redirect()->route('rounds.show', $round);
    }

    public function destroy(Game $game): RedirectResponse
    {
        $round = $game->round;

        $this->authorize('update', $round);

        if ($response = $this->rejectUnlessLastGameOfOfflineRound($game, 'löschen')) {
            return $response;
        }

        $game->delete();
        $round->unsetRelation('games');

        $this->announceChange($round);

        return redirect()->route('rounds.show', $round);
    }

    private function rejectUnlessLastGameOfOfflineRound(Game $game, string $action): ?RedirectResponse
    {
        $round = $game->round;

        if ($round->isLive()) {
            Toast::error(self::LIVE_ROUND_MESSAGE);

            return back();
        }

        if ($game->isNot($round->getLastGame())) {
            Toast::error("Du kannst nur das letzte Spiel einer Runde {$action}!");

            return back();
        }

        return null;
    }

    private function announceChange(Round $round): void
    {
        broadcast(new RoundUpdated($round->id))->toOthers();
        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($round->groups, $round->updated_at);
    }
}
