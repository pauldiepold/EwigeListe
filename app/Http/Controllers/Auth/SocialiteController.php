<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialiteUserResource;
use App\Models\SocialiteUser;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        if (! ($provider == 'google' || $provider == 'facebook')) {
            return redirect('/login');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (! ($provider == 'google' || $provider == 'facebook')) {
            return redirect('/login');
        }

        try {
            $socialiteUser = SocialiteUser::firstOrCreate(
                Socialite::driver($provider)->stateless()->user(),
                $provider
            );
        } catch (ClientException $e) {
            return redirect()->route('auth.socialite', ['provider' => 'facebook']);
        }

        if ($socialiteUser->user) {
            auth()->login($socialiteUser->user, true);

            return redirect('/#');
        } else {
            if (auth()->check()) {
                $socialiteUser->user_id = auth()->id();
                $socialiteUser->save();
                $socialiteUser->refresh();

                return redirect($socialiteUser->user->player->path().'#');
            } else {
                return redirect()->route('auth.registerOrAttach', ['socialiteUser' => $socialiteUser]);
            }
        }
    }

    public function showView(SocialiteUser $socialiteUser): Response
    {
        $existingUser = User::where('email', $socialiteUser->email)->exists();

        return Inertia::render('Auth/RegisterOrAttach', [
            'socialiteUser' => new SocialiteUserResource($socialiteUser),
            'existingUserEmail' => $existingUser ? $socialiteUser->email : null,
        ]);
    }
}
