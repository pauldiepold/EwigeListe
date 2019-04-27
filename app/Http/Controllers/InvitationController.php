<?php

namespace App\Http\Controllers;

use App\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class InvitationController extends Controller {

    public function index()
    {
        $invitation = Auth::user()->player->invitation;

        return view('invitations.index', compact('invitation'));
    }

    public function store()
    {
        $player = Auth::user()->player;
        if ($player->invitation)
        {
            return Redirect::back()->withErrors(['Du kannst nicht mehr als eine Einladung erstellen!']);
        }

        $valid_until = date("Y-m-d H:i:s", time() + 60 * 60 * 24);

        $pin = rand(pow(10, 3), pow(10, 4) - 1);

        Invitation::create([
            'pin' => $pin,
            'valid_until' => $valid_until,
            'player_id' => $player->id
        ]);

        return redirect()->route('showInvitation');
    }

    public function destroy(Invitation $invitation)
    {
        if (Auth::user()->player->id != $invitation->player_id)
        {
            return Redirect::back()->withErrors(['Du kannst nur deine eigene Einladung löschen!']);
        }
        $invitation->delete();

        return redirect()->route('showInvitation');
    }

    public function destroyOld()
    {
        $invitations = Invitation::all();

        foreach ($invitations as $invitation)
        {
            if (strtotime($invitation->valid_until) <= time())
            {
                $invitation->delete();
            }
        }

        return redirect()->route('showInvitation');
    }
}