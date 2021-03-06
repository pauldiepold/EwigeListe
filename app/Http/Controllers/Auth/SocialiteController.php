<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use App\SocialiteUser;
use App\User;
use function GuzzleHttp\Promise\queue;


class SocialiteController extends Controller
{

    public function redirect($provider)
    {
        if (!($provider == "google" || $provider == "facebook"))
        {
            return redirect('/login');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (!($provider == "google" || $provider == "facebook"))
        {
            return redirect('/login');
        }

        try
        {
            $socialiteUser = SocialiteUser::firstOrCreate(
                Socialite::driver($provider)->stateless()->user(),
                $provider
            );
        } catch (\GuzzleHttp\Exception\ClientException $e)
        {
            return redirect()->route('auth.socialite', ['provider' => 'facebook']);
        }

        if ($socialiteUser->user)
        {
            auth()->login($socialiteUser->user, true);

            return redirect('/#');
        } else
        {
            if (auth()->check())
            {
                $socialiteUser->user_id = auth()->id();
                $socialiteUser->save();
                $socialiteUser->refresh();

                return redirect($socialiteUser->user->player->path() . '#');
            } else
            {
                return redirect()->route('auth.registerOrAttach', ['socialiteUser' => $socialiteUser]);
            }
        }
    }

    public function showView(SocialiteUser $socialiteUser) {
        $user = User::where('email', $socialiteUser->email)->first();

        return view('auth.registerOrAttach', compact('socialiteUser', 'user'));
    }
}
