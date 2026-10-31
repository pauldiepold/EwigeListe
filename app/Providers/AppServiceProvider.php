<?php

namespace App\Providers;

use App\Http\Toast;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        setlocale(LC_TIME, config('app.locale'));
        Carbon::setLocale(config('app.locale'));

        Inertia::handleExceptionsUsing(fn (ExceptionResponse $response) => $this->renderErrorPage($response));
    }

    /**
     * Keine Inertia-Seite für JSON (Live-Spiel, inklusive 422) und für 503: Während des Deploys werden die
     * Assets neu gebaut, dann greift die statische `errors/503.blade.php`.
     *
     * Ab 500 ohne geteilte Daten, weil diese die Datenbank brauchen, die oft gerade die Ursache ist.
     */
    private function renderErrorPage(ExceptionResponse $response): ExceptionResponse|RedirectResponse|null
    {
        $status = $response->statusCode();

        if ($status < 400 || $status === 503 || $response->request->expectsJson()) {
            return null;
        }

        if ($status === 419) {
            Toast::error('Die Seite war abgelaufen. Bitte versuche es noch einmal.');

            return back(303);
        }

        if ($status < 500) {
            return $response->render('Error', ['status' => $status])->withSharedData();
        }

        if (config('app.debug')) {
            return null;
        }

        Inertia::flushShared();

        return $response->render('Error', ['status' => $status]);
    }
}
