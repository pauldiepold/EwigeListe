<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Wie im Vue-Starter-Kit: eine Stelle für die Passwortregeln von Registrierung, Reset und
 * Passwortwechsel. Die Meldungen stehen hier, weil die deutschen Sprachdateien nicht greifen.
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

    /**
     * @return array<string, string>
     */
    protected function passwordMessages(): array
    {
        return [
            'password.required' => 'Es muss ein Passwort angegeben werden!',
            'password.min' => 'Das Passwort muss mindestens :min Zeichen haben!',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein!',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function currentPasswordMessages(): array
    {
        return [
            'current_password.required' => 'Das alte Passwort muss angegeben werden!',
            'current_password.current_password' => 'Das alte Passwort stimmt nicht!',
        ];
    }
}
