<?php

namespace App\Http\Requests;

use App\Concerns\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Wie `PasswordUpdateRequest` im Vue-Starter-Kit. Der Admin ändert Passwörter (auch fremde) ohne
 * das alte; `Users/Edit` blendet ihm das Feld deshalb aus.
 */
class UpdatePassword extends FormRequest
{
    use PasswordValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => $this->user()->isAdmin() ? [] : $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->passwordMessages();
    }
}
