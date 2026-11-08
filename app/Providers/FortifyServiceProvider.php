<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\PasswordResetResponse;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Laravel\Fortify\Fortify;

/**
 * Aufgebaut wie im Vue-Starter-Kit (`docs/adr/0002-fortify-statt-laravel-ui.md`). Abweichung:
 * Nach dem Reset erscheint die Meldung als Toast statt als Session-`status`. Nach der
 * Link-Anforderung bestätigt `Auth/ForgotPassword` selbst, das `status` bleibt dort ungenutzt.
 */
class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PasswordResetResponseContract::class, PasswordResetResponse::class);
    }

    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
        $this->configureResetPasswordMail();
    }

    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Die Passwort-Bestätigung registriert Fortify immer. Kein eingeschaltetes Feature braucht
     * sie, deshalb gibt es die Seite nicht (404 statt 500 ohne View).
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn () => Inertia::render('Auth/Login'));

        Fortify::registerView(fn () => Inertia::render('Auth/Register', [
            'captchaSiteKey' => config('captcha.sitekey'),
        ]));

        Fortify::requestPasswordResetLinkView(fn () => Inertia::render('Auth/ForgotPassword'));

        Fortify::resetPasswordView(fn (Request $request) => Inertia::render('Auth/ResetPassword', [
            'token' => $request->route('token'),
            'email' => $request->query('email', ''),
        ]));

        Fortify::confirmPasswordView(fn () => abort(404));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }

    private function configureResetPasswordMail(): void
    {
        ResetPassword::toMailUsing(fn (User $user, string $token): MailMessage => (new MailMessage)
            ->greeting('Hallo!')
            ->subject('Passwort zurücksetzen')
            ->line('Um mit einem neuen Passwort weiterhin Doppelkopf zu spielen, setze dein Passwort zurück:')
            ->action('Passwort zurücksetzen', route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()]))
            ->line('Dieser Link ist '.config('auth.passwords.users.expire').' Minuten gültig.')
            ->salutation('Beste Grüße, deine Ewige Liste.'));
    }
}
