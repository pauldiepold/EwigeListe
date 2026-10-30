<?php

namespace App\Http\Controllers\Auth;

use App\Group;
use App\Http\Controllers\Controller;
use App\Player;
use App\SocialiteUser;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    public function __construct()
    {
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
            'name.required' => 'Es muss ein Vorname angegeben werden!',
            'email.required' => 'Es muss eine E-Mail Adresse angegeben werden!',
            'password.required' => 'Es muss ein Passwort angegeben werden!',
            'password.min' => 'Das Passwort muss mindestens 6 Zeichen haben!',
            'password.confirmed' => 'Die Passwörter stimmen nicht überein!',
            'g-recaptcha-response.required' => 'Bitte bestätige, dass du kein Roboter bist.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA-Fehler! Versuche es später erneut oder kontaktiere den Administrator.',
        ];

        return Validator::make($data, [
            'surname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required_without:socialiteUserId|string|min:6|confirmed',
            'socialiteUserId' => 'exists:socialite_users,id',
            'g-recaptcha-response' => 'required_without:socialiteUserId|captcha',
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        $player = Player::create([
            'surname' => $data['surname'],
            'name' => $data['name'],
        ]);

        $socialiteUserId = isset($data['socialiteUserId']) ? $data['socialiteUserId'] : null;

        $password = $socialiteUserId ? Str::random(14) : $data['password'];

        $user = User::create([
            'player_id' => $player->id,
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);

        if ($socialiteUserId) {
            $socialiteUser = SocialiteUser::find($socialiteUserId);
            if ($socialiteUser->user_id == null) {
                $socialiteUser->user_id = $user->id;
                $socialiteUser->save();
            }
        }

        Group::find(1)->addPlayer($player);

        $player->profiles->first()->calculate();

        return $user;
    }

    protected function registered(Request $request, $user) {}
}
