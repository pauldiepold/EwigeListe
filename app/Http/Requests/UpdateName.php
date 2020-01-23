<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateName extends FormRequest
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
            'vorname' => 'required|string|min:2',
            'nachname' => 'required|string|min:2'
        ];
    }

    public function attributes()
    {
        return [
            'vorname' => 'Vorname',
            'nachname' => 'Nachname'
        ];
    }
}
