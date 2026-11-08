<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Wie im Vue-Starter-Kit: eine Stelle für die Passwortregeln von Registrierung, Reset und
 * Passwortwechsel.
 */
trait PasswordValidationRules
{
    /**
     * @return array<int, Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }

    /**
     * @return array<int, Rule|array<mixed>|string>
     */
    protected function currentPasswordRules(): array
    {
        return ['required', 'string', 'current_password'];
    }
}
