<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;


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

        $socialiteUser = Socialite::driver($provider)->stateless()->user();

        dd($socialiteUser);
    }
}
