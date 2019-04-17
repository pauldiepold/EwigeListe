<?php

namespace App\Http\Controllers;

use App\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class InviteController extends Controller
{

    public function index()
    {
        $invites = Auth::user()->player->invites;
		
		return view('invites.index', compact('invites'));
    }

    public function store()
    {
		$oldInvites = Auth::user()->player->invites->count();
		
		if ($oldInvites > 3) {
			return Redirect::back()->withErrors(['Du kannst nicht mehr als drei Einladungen erstellen!']);
		}
		
		$valid_until = date("Y-m-d H:i:s", ceil( (time()+60*60*24)/3600)*3600 );
		
		$pin = rand(pow(10, 3), pow(10, 4)-1);
		
        $invite = Invite::create([
			'pin' => $pin,
			'valid_until' => $valid_until,
		]);
		
		Auth::user()->player->invites()->save($invite);
		
		return redirect()->route('showInvites');
    }

    public function edit(Invite $invite)
    {
        //
    }
	
    public function destroy(Invite $invite)
    {
        $invites = Invite::all();
		
		foreach ($invites as $invite) {
			if (strtotime($invite->valid_until) <= time()) {
				$invite->delete();
			}
		}
    }
}