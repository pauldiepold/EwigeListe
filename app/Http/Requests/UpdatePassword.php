<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends FormRequest
{

    protected function getRedirectUrl()
    {
        return parent::getRedirectUrl() . '#passwort';
    }

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
            'current_password' => [
                'required',
                'min:6',
                function ($attribute, $value, $fail)
                {
                    if (!Hash::check($value, Auth::user()->password) && Auth::id() != 1)
                    {
                        $fail('Das alte Passwort stimmt nicht!');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed'
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => 'Das alte Passwort',
            'password' => 'Neues Passwort'
        ];
    }
}
