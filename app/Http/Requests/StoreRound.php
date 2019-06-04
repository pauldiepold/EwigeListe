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
            'numberOfPlayers' => 'required|between:4,7|integer',
            'players' => 'required|size:7|array',
            'players.*' => 'exists:players,id',
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
        });
    }

    public function messages()
    {
        return [

        ];
    }
}
