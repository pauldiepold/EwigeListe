<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PendingSocialiteLink;
use App\Services\PlayerRegistration;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private PlayerRegistration $playerRegistration,
        private PendingSocialiteLink $pendingSocialiteLink,
    ) {
        $this->middleware('guest');
    }

    public function showRegistrationForm(): Response
    {
        return Inertia::render('Auth/Register', [
            'captchaSiteKey' => config('captcha.sitekey'),
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'surname.required' => 'Es muss ein Vorname angegeben werden!',
            'name.required' => 'Es muss ein Nachname angegeben werden!',
            'email.required' => 'Es muss eine E-Mail Adresse angegeben werden!',
            'password.required' => 'Es muss ein Passwort angegeben werden!',
            'password.min' => 'Das Passwort muss mindestens 6 Zeichen haben!',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein!',
            'g-recaptcha-response.required' => 'Bitte bestätige, dass du kein Roboter bist.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA-Fehler! Versuche es später erneut oder kontaktiere den Administrator.',
        ];

        $viaSocialite = $this->registersViaSocialite();

        return Validator::make($data, [
            'surname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => $viaSocialite ? [] : 'required|string|min:6|confirmed',
            'g-recaptcha-response' => $viaSocialite ? [] : 'required|captcha',
        ], $messages);
    }

    /**
     * Mit gemerkter Google- oder Facebook-Identität bekommt der Benutzer ein Zufallspasswort; die
     * Verknüpfung übernimmt der Login direkt danach (`LinkPendingSocialiteUser`).
     *
     * @param  array<string, mixed>  $data
     */
    protected function create(array $data): User
    {
        return $this->playerRegistration->register(
            surname: $data['surname'],
            name: $data['name'],
            email: $data['email'],
            password: $this->registersViaSocialite() ? Str::random(14) : $data['password'],
        );
    }

    /**
     * Captcha und Passwort entfallen nur, wenn der OAuth-Callback eine Identität in der Session
     * hinterlegt hat; sie lässt sich nicht per Formular vortäuschen.
     */
    private function registersViaSocialite(): bool
    {
        return $this->pendingSocialiteLink->socialiteUser() !== null;
    }

    protected function registered(Request $request, $user) {}
}
