<?php

namespace App\Http\Requests;

use App\Group;
use App\Round;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class UpdateRound extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->round());
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'groups' => 'required|array',
            'groups.*' => 'integer|exists:groups,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $playerIds = $this->round()->players->pluck('id');
            $newGroups = Group::query()
                ->whereIn('id', $this->input('groups'))
                ->whereKeyNot(Group::EWIGE_LISTE_ID)
                ->whereNotIn('id', $this->round()->groups->pluck('id'))
                ->get();

            foreach ($newGroups as $group) {
                if ($group->closed) {
                    $validator->errors()->add('groups', "Die Liste {$group->name} ist geschlossen.");
                } elseif (! $group->hasAnyPlayer($playerIds)) {
                    $validator->errors()->add('groups', "Keiner der Spieler ist Mitglied der Liste {$group->name}.");
                }
            }
        });
    }

    /**
     * Die gewählten Listen. Die Ewige Liste und geschlossene Listen der Runde bleiben immer erhalten.
     *
     * @return Collection<int, int>
     */
    public function groupIds(): Collection
    {
        return collect($this->validated('groups'))
            ->map(fn (mixed $groupId): int => (int) $groupId)
            ->push(Group::EWIGE_LISTE_ID)
            ->concat($this->round()->groups->where('closed', true)->pluck('id'))
            ->unique()
            ->values();
    }

    private function round(): Round
    {
        return $this->route('round');
    }
}
