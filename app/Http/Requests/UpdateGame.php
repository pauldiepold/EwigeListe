<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGame extends FormRequest {

    protected $errorBag = 'update';

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
            'updateWinners' => 'required|max:3|array',
            'updateWinners.*' => 'integer',
            'updatePoints' => 'required|integer|nullable|between:-4,16',
        ];
    }

    public function messages()
    {
        return [
            'updateWinners.required' => 'Es muss mindestens ein Gewinner ausgewählt werden!',
            'updateWinners.max' => 'Es dürfen höchstens drei Gewinner ausgewählt werden!',
            'updatePoints.required' => 'Es muss eine Punktzahl angegeben werden!'
        ];
    }
}
