<?php

namespace App\Http\Middleware;

use App\Http\Toast;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Versteckte Spieler können sich nicht einloggen. Die Prüfung läuft bei jedem Request, weil es
 * neben dem Login-Formular weitere Wege hinein gibt: Socialite, „Passwort zurücksetzen“, laufende
 * Sessions und „Angemeldet bleiben“. Der Admin ist ausgenommen, damit er sich nicht selbst aussperrt.
 */
class LogOutHiddenPlayers
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->player->hide || $user->isAdmin()) {
            return $next($request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Toast::error('Dein Konto ist gesperrt. Wende dich bitte an den Administrator.');

        return redirect()->route('login');
    }
}
