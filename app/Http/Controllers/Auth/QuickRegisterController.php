<?php

namespace App\Http\Controllers\Auth;

use App\Group;
use App\User;
use App\Player;
use App\Http\Controllers\Controller;
use App\Http\Toast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

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
     * Schnellregistrierung aus „Neue Runde“. Die Person loggt sich nicht ein, der Benutzer
     * bekommt daher eine Platzhalter-E-Mail. `addedPlayerId` lässt `Rounds/Create` den
     * neuen Spieler direkt auswählen.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $player = Player::create([
            'surname' => $request->surname,
            'name' => $request->name
        ]);

        $password = Str::random(14);

        User::create([
            'player_id' => $player->id,
            'email' => $this->placeholderEmail(),
            'password' => Hash::make($password),
        ]);

        // Füge Spieler zur Standard-Gruppe hinzu
        Group::find(1)->addPlayer($player);

        // Berechne das erste Profil
        $player->profiles->first()->calculate();

        Inertia::flash('addedPlayerId', $player->id);
        Toast::success('Spieler ' . $player->surname . ' ' . $player->name . ' wurde erfolgreich erstellt!');

        return redirect()->route('rounds.create');
    }

    /**
     * Eindeutig und nie zustellbar (`.invalid` ist laut RFC 2606 reserviert). Soll sich die
     * Person später doch einloggen, trägt man ihre echte E-Mail nach.
     */
    private function placeholderEmail(): string
    {
        return 'schnellregistrierung-' . Str::uuid() . '@ewigeliste.invalid';
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
        ];

        return Validator::make($data, [
            'surname' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ], $messages);
    }
} 