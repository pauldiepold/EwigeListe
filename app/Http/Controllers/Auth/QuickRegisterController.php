<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Toast;
use App\Services\PlayerRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
    public function store(Request $request, PlayerRegistration $playerRegistration): RedirectResponse
    {
        $this->validator($request->all())->validate();

        $player = $playerRegistration->register(
            $request->surname,
            $request->name,
            $this->placeholderEmail(),
            Str::random(14),
        )->player;

        Inertia::flash('addedPlayerId', $player->id);
        Toast::success('Spieler '.$player->surname.' '.$player->name.' wurde erfolgreich erstellt!');

        return redirect()->route('rounds.create');
    }

    /**
     * Eindeutig und nie zustellbar (`.invalid` ist laut RFC 2606 reserviert). Soll sich die
     * Person später doch einloggen, trägt man ihre echte E-Mail nach.
     */
    private function placeholderEmail(): string
    {
        return 'schnellregistrierung-'.Str::uuid().'@ewigeliste.invalid';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
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
