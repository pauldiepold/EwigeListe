<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Player;
use App\Profile;
use App\Invitation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller {

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

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
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
        ];

        return Validator::make($data, [
            'surname' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */
    protected function create(array $data)
    {
        $player = Player::create([
            'surname' => $data['surname'],
            'name' => $data['name']
        ]);

        $user = User::create([
            'player_id' => $player->id,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }
}
