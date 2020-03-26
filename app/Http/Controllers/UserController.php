<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmail;
use App\Http\Requests\UpdateListen;
use App\Http\Requests\UpdateName;
use App\Http\Requests\UpdatePassword;
use App\Player;
use App\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function edit(User $user)
    {
        $player = $user->player;
        $this->authorize('update', $user);

        $player->load('user', 'groups', 'profiles.group');

        $profiles = $player->profiles;

        return view('users.edit', compact('player', 'profiles'));
    }

    public function updateName(User $user, UpdateName $request)
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $player = $user->player;
        $player->surname = $validatedData['vorname'];
        $player->name = $validatedData['nachname'];
        $player->save();

        return redirect(route('users.edit', [$player]) . '#name')
            ->with('message', 'Name wurde erfolgreich gespeichert!');
    }

    public function updateMail(User $user, UpdateEmail $request)
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $user->email = $validatedData['email'];
        $user->save();

        return redirect(route('users.edit', [$user->player]) . '#email')
            ->with('message', 'E-Mail wurde erfolgreich geändert!');
    }

    public function updatePassword(User $user, UpdatePassword $request)
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return redirect(route('users.edit', [$user->player]) . '#passwort')
            ->with('message', 'Passwort wurde erfolgreich geändert!');
    }

    public function updateListen(User $user, UpdateListen $request)
    {
        $this->authorize('update', $user);

        $validatedData = collect($request->validated()['groups'])
            ->filter(function ($value, $key)
            {
                return $value['group_id'] != 1 && !$value['closed'];
            });

        $profiles = Profile::find($validatedData->pluck('profile_id'));

        foreach ($profiles as $profile)
        {
            $profile->default = $validatedData->where('profile_id', $profile->id)
                                    ->first()['default'];
            $profile->save();
        }

        return 'success';
    }

}
