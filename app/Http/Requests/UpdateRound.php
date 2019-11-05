<?php

namespace App\Http\Requests;

use App\Round;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRound extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'groups' => 'array|min:1',
            'groups.*' => 'exists:groups,id'
        ];
    }
}
