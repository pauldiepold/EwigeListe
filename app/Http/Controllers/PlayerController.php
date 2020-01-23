<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmail;
use App\Http\Requests\UpdateListen;
use App\Http\Requests\UpdateName;
use App\Http\Requests\UpdatePassword;
use App\Player;
use App\Group;
use App\Profile;
use App\Round;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PlayerController extends Controller
{

    public function show(Player $player, Group $group = null)
    {
        $selectedGroup = !$group ? Group::find(1) : $group;

        $player->load(['profiles.group', 'groups', 'rounds', 'games', 'badges']);

        $badges = $player->badges()
            ->where('group_id', $selectedGroup->id)
            ->with(['player', 'group'])
            ->get()
            ->groupBy('type');

        $profile = $player->profiles()
            ->where('group_id', $selectedGroup->id)
            ->first();

        $rounds_count = $player->rounds()
            ->whereHas('groups', function (Builder $query) use ($selectedGroup)
            {
                $query->where('groups.id', '=', $selectedGroup->id);
            })
            ->count();

        return view('players.show', compact('player', 'profile', 'rounds_count', 'selectedGroup', 'badges'));

    }

    public function edit(Player $player)
    {
        $this->authorize('update', $player);

        $player->load('user', 'groups', 'profiles.group');

        $profiles = $player->profiles;

        return view('players.edit', compact('player', 'profiles'));
    }

    public function updateName(Player $player, UpdateName $request)
    {
        $this->authorize('update', $player);

        $validatedData = $request->validated();

        $player->surname = $validatedData['vorname'];
        $player->name = $validatedData['nachname'];
        $player->save();

        return redirect(route('players.edit', [$player]) . '#name')
            ->with('message', 'Name wurde erfolgreich gespeichert!');
    }

    public function updateMail(Player $player, UpdateEmail $request)
    {
        $this->authorize('update', $player);

        $validatedData = $request->validated();

        $user = $player->user;

        $user->email = $validatedData['email'];
        $user->save();

        return redirect(route('players.edit', [$player]) . '#email')
            ->with('message', 'E-Mail wurde erfolgreich geändert!');
    }

    public function updatePassword(Player $player, UpdatePassword $request)
    {
        $this->authorize('update', $player);

        $validatedData = $request->validated();

        $user = $player->user;

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        return redirect(route('players.edit', [$player]) . '#passwort')
            ->with('message', 'Passwort wurde erfolgreich geändert!');
    }

    public function updateListen(Player $player, UpdateListen $request)
    {
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
