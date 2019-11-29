<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use App\SocialiteUser;


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

        $socialiteUser = SocialiteUser::firstOrCreate(
            Socialite::driver($provider)->stateless()->user(),
            $provider
        );

        if ($socialiteUser->user)
        {
            auth()->login($socialiteUser->user);

            return redirect('/#');
        } else
        {
            if (auth()->check())
            {
                $socialiteUser->user_id = auth()->id();
                $socialiteUser->save();
                $socialiteUser->refresh();

                return redirect($socialiteUser->user->player->path() . '#');
            } else {
                return redirect()->route('auth.registerOrAttach');
            }
        }
    }
}
