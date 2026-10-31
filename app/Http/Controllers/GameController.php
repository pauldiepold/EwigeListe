<?php

namespace App\Http\Controllers;

use App\Events\RoundUpdated;
use App\Game;
use App\Group;
use App\Http\Requests\StoreGame;
use App\Http\Toast;
use App\Profile;
use App\Round;
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

    public function destroy(Game $game): RedirectResponse
    {
        $round = $game->round;

        $this->authorize('update', $round);

        if ($round->isLive()) {
            Toast::error(self::LIVE_ROUND_MESSAGE);

            return back();
        }

        if ($game->isNot($round->getLastGame())) {
            Toast::error('Du kannst nur das letzte Spiel einer Runde löschen!');

            return back();
        }

        $game->delete();
        $round->unsetRelation('games');

        $this->announceChange($round);

        return redirect()->route('rounds.show', $round);
    }

    private function announceChange(Round $round): void
    {
        broadcast(new RoundUpdated($round->id))->toOthers();
        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($round->groups, $round->updated_at);
    }
}
