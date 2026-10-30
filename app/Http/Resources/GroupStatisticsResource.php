<?php

namespace App\Http\Resources;

use App\Group;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Group
 */
class GroupStatisticsResource extends JsonResource
{
    public static $wrap = null;

    /**
     * @return array{records: list<array{label: string, value: string, holders: list<array{name: string, path: string}>}>, stats: list<array{label: string, value: string}>}
     */
    public function toArray(Request $request): array
    {
        return [
            'records' => $this->recordRows()->map(fn (array $record): array => [
                'label' => $record['label'],
                'value' => $record['value'],
                'holders' => $record['profiles']->map(fn (Profile $profile): array => [
                    'name' => $profile->player->surname,
                    'path' => $profile->path(),
                ])->all(),
            ])->all(),
            'stats' => [
                ['label' => 'Spiele insgesamt:', 'value' => (string) (int) $this->totalGames],
                ['label' => '∅ Punktzahl pro Spiel:', 'value' => (string) (float) $this->pointsPerGame],
                ['label' => '∅ Anzahl Spiele pro Runde:', 'value' => (string) (float) $this->gamesPerRound],
            ],
        ];
    }
}
