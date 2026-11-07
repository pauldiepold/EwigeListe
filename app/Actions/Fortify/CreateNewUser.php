<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use App\Services\PendingSocialiteLink;
use App\Services\PlayerRegistration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct(
        private PlayerRegistration $playerRegistration,
        private PendingSocialiteLink $pendingSocialiteLink,
    ) {}

    /**
     * Mit gemerkter Google- oder Facebook-Identität entfallen Captcha und Passwort; der Benutzer
     * bekommt ein Zufallspasswort. Die Verknüpfung übernimmt der Login direkt danach
     * (`LinkPendingSocialiteUser`). Vortäuschen lässt sich das nicht, die Identität kommt aus der
     * Session.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $viaSocialite = $this->pendingSocialiteLink->socialiteUser() !== null;

        Validator::make($input, [
            'surname' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $viaSocialite ? [] : $this->passwordRules(),
            'g-recaptcha-response' => $viaSocialite ? [] : ['required', 'captcha'],
        ], [
            'surname.required' => 'Es muss ein Vorname angegeben werden!',
            'name.required' => 'Es muss ein Nachname angegeben werden!',
            'email.required' => 'Es muss eine E-Mail Adresse angegeben werden!',
            'g-recaptcha-response.required' => 'Bitte bestätige, dass du kein Roboter bist.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA-Fehler! Versuche es später erneut oder kontaktiere den Administrator.',
            ...$this->passwordMessages(),
        ])->validate();

        return $this->playerRegistration->register(
            surname: $input['surname'],
            name: $input['name'],
            email: $input['email'],
            password: $viaSocialite ? Str::random(14) : $input['password'],
        );
    }
}
