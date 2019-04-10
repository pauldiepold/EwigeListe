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
            'player1' => 'required|integer|exists:players,id',
            'player2' => 'required|integer|exists:players,id',
            'player3' => 'required|integer|exists:players,id',
            'player4' => 'required|integer|exists:players,id',
            'player5' => 'integer|exists:players,id',
            'player6' => 'integer|exists:players,id',
            'player7' => 'integer|exists:players,id',
        ];
    }

    public function messages()
    {
        return [

        ];
    }
}
