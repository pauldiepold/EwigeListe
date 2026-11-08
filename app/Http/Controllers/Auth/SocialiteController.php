<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialiteUserResource;
use App\Http\Toast;
use App\Models\SocialiteUser;
use App\Models\User;
use App\Services\PendingSocialiteLink;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class SocialiteController extends Controller
{
    private const PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): SymfonyRedirectResponse
    {
        if (! in_array($provider, self::PROVIDERS, true)) {
            return redirect()->route('login');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Eine Identität ohne Konto merkt sich die Session für `showView`. Ein falscher OAuth-`state`
     * (Callback nicht aus diesem Browser angestoßen) führt zurück zum Login und nicht erneut zum
     * Provider, sonst entstünde bei fehlendem Session-Cookie eine Endlosschleife.
     */
    public function callback(string $provider, PendingSocialiteLink $pendingSocialiteLink): RedirectResponse
    {
        if (! in_array($provider, self::PROVIDERS, true)) {
            return redirect()->route('login');
        }

        try {
            $socialiteUser = SocialiteUser::firstOrCreate(Socialite::driver($provider)->user(), $provider);
        } catch (ClientException) {
            return redirect()->route('auth.socialite', ['provider' => $provider]);
        } catch (InvalidStateException) {
            Toast::error('Die Anmeldung mit '.ucfirst($provider).' ist fehlgeschlagen. Bitte versuche es noch einmal.');

            return redirect()->route('login');
        }

        if ($socialiteUser->user) {
            auth()->login($socialiteUser->user, true);

            return redirect('/#');
        }

        if (auth()->check()) {
            $socialiteUser->update(['user_id' => auth()->id()]);

            return redirect($socialiteUser->fresh()->user->player->path().'#');
        }

        $pendingSocialiteLink->remember($socialiteUser);

        return redirect()->route('auth.registerOrAttach');
    }

    public function showView(PendingSocialiteLink $pendingSocialiteLink): Response|RedirectResponse
    {
        $socialiteUser = $pendingSocialiteLink->socialiteUser();

        if ($socialiteUser === null) {
            return redirect()->route('login');
        }

        $existingUser = User::where('email', $socialiteUser->email)->exists();

        return Inertia::render('Auth/RegisterOrAttach', [
            'socialiteUser' => new SocialiteUserResource($socialiteUser),
            'existingUserEmail' => $existingUser ? $socialiteUser->email : null,
        ]);
    }
}
