<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Player;
use App\Invite;
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
    protected $redirectTo = '/home';

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
        return Validator::make($data, [
            'surname' => ['required', 'string', 'max:255',
                          function ($attribute, $value, $fail)
                          {
                              if (strpos($value, ' ') !== false)
                              {
                                  $fail('Der Vorname darf kein Leerzeichen enthalten!');
                              }
                          }
            ],
            'name' => ['required', 'string', 'max:255',
                       function ($attribute, $value, $fail)
                       {
                           if (strpos($value, ' ') !== false)
                           {
                               $fail('Der Name darf kein Leerzeichen enthalten!');
                              }
                       }
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'pin' => ['required', 'integer', 'digits:4', 'exists:invites,pin'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\User
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

        $player->user()->save($user);

        Invite::where('pin', $data['pin'])->first()->delete();

        return $user;
    }
}
