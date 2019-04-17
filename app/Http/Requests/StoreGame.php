<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGame extends FormRequest {

    protected $errorBag = 'create';

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
            'winners' => 'required|max:3|array',
            'winners.*' => 'integer',
            'points' => 'required|integer|nullable|between:-4,16',
        ];
    }

    public function messages()
    {
        return [
            'winners.required' => 'Es muss mindestens ein Gewinner ausgewählt werden!',
            'winners.max' => 'Es dürfen höchstens drei Gewinner ausgewählt werden!',
            'points.required' => 'Es muss eine Punktzahl angegeben werden!'
        ];
    }
}
