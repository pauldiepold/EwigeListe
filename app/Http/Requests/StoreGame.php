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
            'winners.*' => 'integer|exists:players,id',
            'points' => 'required|integer|nullable|between:-4,16',
            'misplayed' => 'boolean'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator)
        {
            if ($this->input('misplayed') && count($this->input('winners')) != 3)
            {
                $validator->errors()->add('misplayed', 'Wenn sich jemand vergibt, muss dies als verlorenes Solo eingetragen werden!');
            }
            if ($this->input('misplayed') && $this->input('points') < 2)
            {
                $validator->errors()->add('points', 'Wenn sich jemand vergibt, verliert er mit mindestens 2 Punkten!');
            }
        });
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
