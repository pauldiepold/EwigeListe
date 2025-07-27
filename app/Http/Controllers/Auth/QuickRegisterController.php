<?php

namespace App\Http\Controllers\Auth;

use App\Group;
use App\User;
use App\Player;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class QuickRegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the quick registration form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.quick-register');
    }

    /**
     * Handle a quick registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $player = Player::create([
            'surname' => $request->surname,
            'name' => $request->name
        ]);

        $password = Str::random(14);

        $user = User::create([
            'player_id' => $player->id,
            'email' => $request->email,
            'password' => Hash::make($password),
        ]);

        // Füge Spieler zur Standard-Gruppe hinzu
        Group::find(1)->addPlayer($player);

        // Berechne das erste Profil
        $player->profiles->first()->calculate();

        return redirect()->route('rounds.create')
            ->with('success', 'Spieler ' . $player->surname . ' ' . $player->name . ' wurde erfolgreich erstellt!');
    }

    /**
     * Get a validator for an incoming quick registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'surname.required' => 'Es muss ein Vorname angegeben werden!',
            'name.required' => 'Es muss ein Nachname angegeben werden!',
            'email.required' => 'Es muss eine E-Mail Adresse angegeben werden!',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail Adresse ein!',
            'email.unique' => 'Diese E-Mail Adresse wird bereits verwendet!',
        ];

        return Validator::make($data, [
            'surname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ], $messages);
    }
} 