<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRound extends FormRequest {

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
            'groups.*' => 'exists:groups,id'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator)
        {
            if (collect($this->input('players'))->unique()->count() != collect($this->input('players'))->count())
            {
                $validator->errors()->add('players', 'Es darf kein Spieler doppelt ausgewählt werden!');
            }

            if (!collect($this->input('players'))->contains(auth()->user()->player->id))
            {
                $validator->errors()->add('players', 'Du selbst musst Teil der Runde sein!');
            }
        });
    }

    public function messages()
    {
        return [
            //'groups.required' => 'Es muss mindestens eine Gruppe ausgewählt werden!'
        ];
    }
}
