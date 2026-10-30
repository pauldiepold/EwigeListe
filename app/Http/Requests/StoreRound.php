<?php

namespace App\Http\Requests;

use App\Group;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRound extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'players' => 'required|between:4,7|array',
            'players.*' => 'exists:players,id',
            'groups' => 'array',
            'groups.*' => 'exists:groups,id',
            'liveGame' => 'required|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (collect($this->input('players'))->unique()->count() != collect($this->input('players'))->count()) {
                $validator->errors()->add('players', 'Es darf kein Spieler doppelt ausgewählt werden!');
            }

            if (! collect($this->input('players'))->contains(auth()->user()->player->id)) {
                $validator->errors()->add('players', 'Du selbst musst Teil der Runde sein!');
            }

            $this->validateGroups($validator);
        });
    }

    /**
     * Eine Runde darf nur offenen Listen hinzugefügt werden, in denen mindestens einer ihrer Spieler Mitglied ist.
     * Die Ewige Liste ist davon ausgenommen, sie wird immer angehängt.
     */
    private function validateGroups(Validator $validator): void
    {
        $playerIds = collect($this->input('players'));

        $groups = Group::query()
            ->whereIn('id', collect($this->input('groups')))
            ->whereKeyNot(Group::EWIGE_LISTE_ID)
            ->get();

        foreach ($groups as $group) {
            if ($group->closed) {
                $validator->errors()->add('groups', "Die Liste {$group->name} ist geschlossen.");
            } elseif (! $group->hasAnyPlayer($playerIds)) {
                $validator->errors()->add('groups', "Keiner der Spieler ist Mitglied der Liste {$group->name}.");
            }
        }
    }

    public function messages()
    {
        return [
            'players.between' => 'Eine Runde braucht 4 bis 7 Spieler.',
        ];
    }
}
