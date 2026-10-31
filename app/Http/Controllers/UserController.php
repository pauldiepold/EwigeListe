<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmail;
use App\Http\Requests\UpdateListen;
use App\Http\Requests\UpdateName;
use App\Http\Requests\UpdatePassword;
use App\Http\Resources\EditUserProfileResource;
use App\Http\Resources\EditUserResource;
use App\Http\Toast;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        $user->load('player.profiles.group');

        return Inertia::render('Users/Edit', [
            'user' => (new EditUserResource($user))->resolve(request()),
            'profiles' => EditUserProfileResource::collection($user->player->profiles)->resolve(request()),
        ]);
    }

    public function updateName(User $user, UpdateName $request): RedirectResponse
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $player = $user->player;
        $player->surname = $validatedData['vorname'];
        $player->name = $validatedData['nachname'];
        $player->save();

        Toast::success('Name wurde erfolgreich gespeichert!');

        return redirect()->route('users.edit', $user);
    }

    public function updateMail(User $user, UpdateEmail $request): RedirectResponse
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $user->email = $validatedData['email'];
        $user->save();

        Toast::success('E-Mail wurde erfolgreich geändert!');

        return redirect()->route('users.edit', $user);
    }

    public function updatePassword(User $user, UpdatePassword $request): RedirectResponse
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        Toast::success('Passwort wurde erfolgreich geändert!');

        return redirect()->route('users.edit', $user);
    }

    public function updateListen(User $user, UpdateListen $request): RedirectResponse
    {
        $this->authorize('update', $user);

        $defaultProfileIds = $request->validated()['default_profiles'];

        $user->player->profiles()->with('group')->get()
            ->filter(fn (Profile $profile): bool => $profile->canBeDefault())
            ->each(function (Profile $profile) use ($defaultProfileIds): void {
                $profile->default = in_array($profile->id, $defaultProfileIds);
                $profile->save();
            });

        Toast::success('Standardlisten wurden gespeichert!');

        return redirect()->route('users.edit', $user);
    }
}
