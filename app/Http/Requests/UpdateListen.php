<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListen extends FormRequest
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
            'groups' => 'array|min:1',
            'groups.*.profile_id' => 'exists:profiles,id',
            'groups.*.group_id' => 'exists:groups,id',
            'groups.*.closed' => 'nullable|boolean',
            'groups.*.default' => 'nullable|boolean',
        ];
    }
}
