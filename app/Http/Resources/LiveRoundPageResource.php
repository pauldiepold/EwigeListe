<?php

namespace App\Http\Resources;

use App\Models\LiveRound;
use App\Models\Player;
use App\Models\Round;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Die Runde auf der Live-Seite: Spieler in Sitzreihenfolge, ohne Drehung auf den Betrachter.
 * Die Drehung auf den eigenen Platz übernimmt der Client.
 *
 * @mixin Round
 */
class LiveRoundPageResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{id: int, path: string, gamesCount: int, liveRoundId: int, rules: array<string, bool>, viewerPlayerId: int, players: list<array{id: int, firstName: string, lastName: string, fullName: string, avatarPath: ?string, isActive: bool}>}
     */
    public function toArray(Request $request): array
    {
        $activePlayerIds = $this->getActivePlayers()->pluck('id');

        return [
            'id' => $this->id,
            'path' => $this->path(),
            'gamesCount' => $this->games->count(),
            'liveRoundId' => $this->liveRound->id,
            'rules' => $this->liveRound->only(array_keys(LiveRound::RULE_DEFAULTS)),
            'viewerPlayerId' => $request->user()->player_id,
            'players' => $this->players->map(fn (Player $player): array => [
                'id' => $player->id,
                'firstName' => $player->first_name,
                'lastName' => $player->last_name,
                'fullName' => $player->full_name,
                'avatarPath' => $player->avatar_path,
                'isActive' => $activePlayerIds->contains($player->id),
            ])->values()->all(),
        ];
    }
}
