<?php

namespace App\Http\Resources;

use App\Models\Player;
use App\Models\Round;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Round
 */
class HomeRoundResource extends JsonResource
{
    public static $wrap = null;

    /**
     * `points` ist der Punktestand in dieser Runde (`round_points` lädt der HomeController per Unterabfrage).
     * Die Zeit des letzten Spiels kommt als ISO-Zeitstempel, damit der Browser sie relativ („vor 5 Min.“) anzeigen kann.
     *
     * @return array{id: int, players: list<array{id: int, name: string, avatarPath: ?string, points: int}>, gamesCount: int, lastGameAt: ?string, isLive: bool, isOwn: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'players' => $this->players->map(fn (Player $player): array => [
                'id' => $player->id,
                'name' => $player->surname,
                'avatarPath' => $player->avatar_path,
                'points' => (int) $player->round_points,
            ])->values()->all(),
            'gamesCount' => $this->games_count,
            'lastGameAt' => $this->games_max_created_at ? Carbon::parse($this->games_max_created_at)->toIso8601String() : null,
            'isLive' => $this->isLive(),
            'isOwn' => $this->players->contains('id', $request->user()?->player_id),
        ];
    }
}
