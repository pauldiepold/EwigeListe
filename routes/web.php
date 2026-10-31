<?php

use App\Http\Controllers\Auth\QuickRegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Live\GameController as LiveGameController;
use App\Http\Controllers\Live\RoundController as LiveRoundController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\UserAvatarController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
Verb          Path                        Action  Route Name
GET           /users                      index   users.index
GET           /users/create               create  users.create
POST          /users                      store   users.store
GET           /users/{user}               show    users.show
GET           /users/{user}/edit          edit    users.edit
PUT|PATCH     /users/{user}               update  users.update
DELETE        /users/{user}               destroy users.destroy
*/

Auth::routes();

/* *********** Home ************** */
Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    /* *********** Users *********** */
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('users/{user}/name', [UserController::class, 'updateName'])->name('users.updateName');
    Route::patch('users/{user}/mail', [UserController::class, 'updateMail'])->name('users.updateMail');
    Route::patch('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatePassword');
    Route::patch('users/{user}/listen', [UserController::class, 'updateListen'])->name('users.updateListen');
    Route::post('users/{user}/avatar', [UserAvatarController::class, 'store'])->name('users.updateAvatar');

    /* *********** Players *********** */
    Route::get('profil/{player}/{group?}', [PlayerController::class, 'show'])->name('players.show');

    /* *********** Rounds ************ */
    Route::get('/rundenarchiv/{group?}', [RoundController::class, 'index'])->name('rounds.index');
    Route::get('/runde/erstellen', [RoundController::class, 'create'])->name('rounds.create');
    Route::post('/rounds', [RoundController::class, 'store'])->name('rounds.store');
    Route::get('/rounds/current', [RoundController::class, 'current'])->name('rounds.current');
    Route::get('/runde/{round}', [RoundController::class, 'show'])->name('rounds.show');
    Route::patch('/rounds/{round}', [RoundController::class, 'update'])->name('rounds.update');
    Route::delete('/rounds/{round}', [RoundController::class, 'destroy'])->name('rounds.destroy');
    Route::post('/rounds/{round}/games', [GameController::class, 'store'])->name('games.store');
    Route::patch('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');

    Route::patch('/liveRounds/{liveRound}', [LiveRoundController::class, 'updateRegeln'])->name('liveRounds.updateRegeln');

    /* *********** Groups ************** */
    Route::get('liste', [GroupController::class, 'show'])->name('ewigeListe');
    Route::get('/listen', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/liste/erstellen', [GroupController::class, 'create'])->name('groups.create');
    Route::get('/liste/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');

    Route::post('/liste/{group}/beitreten', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/liste/{group}/verlassen', [GroupController::class, 'leave'])->name('groups.leave');
    Route::patch('/liste/{group}/schließen', [GroupController::class, 'close'])->name('groups.close');

    /* *********** API ************** */
    Route::prefix('api')->group(function () {
        Route::get('/rounds/{round}/fetchData', [RoundController::class, 'fetchData'])->name('rounds.fetchData');

        Route::middleware('can:update,liveRound')->group(function () {
            Route::post('live/{liveRound}/spielStarten', [LiveRoundController::class, 'starteNeuesSpiel'])->name('live.rounds.start');
        });

        Route::middleware('can:update,liveGame')->group(function () {
            Route::post('live/{liveGame}/vorbehalt', [LiveGameController::class, 'vorbehalt'])->name('live.games.vorbehalt');
            Route::post('live/{liveGame}/armutAbgeben', [LiveGameController::class, 'armutAbgeben'])->name('live.games.armutAbgeben');
            Route::post('live/{liveGame}/armutMitnehmen', [LiveGameController::class, 'armutMitnehmen'])->name('live.games.armutMitnehmen');
            Route::post('live/{liveGame}/armutZurueck', [LiveGameController::class, 'armutZurueckgeben'])->name('live.games.armutZurueckgeben');
            Route::post('live/{liveGame}/karteSpielen', [LiveGameController::class, 'karteSpielen'])->name('live.games.karteSpielen');
            Route::post('live/{liveGame}/ansage', [LiveGameController::class, 'ansage'])->name('live.games.ansage');
        });

    });

    /* *********** Admin ************** */
    Route::middleware('admin')->group(function () {
        Route::post('/statistiken/neu-berechnen', [StatisticsController::class, 'recalculate'])->name('statistics.recalculate');
    });
});

/* *********** Statische Seiten (Inertia) ************** */
Route::get('/datenschutz/', [StaticPageController::class, 'datenschutz'])->name('datenschutz');
Route::get('/impressum/', [StaticPageController::class, 'impressum'])->name('impressum');
Route::get('/regeln/', [StaticPageController::class, 'regeln'])->name('regeln');

/* *********** AUTH ************** */
Route::get('auth/redirect/{provider}', [SocialiteController::class, 'redirect'])->name('auth.socialite');
Route::get('callback/{provider}', [SocialiteController::class, 'callback'])->name('auth.socialite.callback');
Route::get('login/social/{socialiteUser}', [SocialiteController::class, 'showView'])->name('auth.registerOrAttach');

/* *********** Quick Register ************** */
Route::middleware('auth')->group(function () {
    Route::post('/register/quick', [QuickRegisterController::class, 'store'])->name('register.quick.store');
});

/* Läuft im web-Stack: Ohne Route gäbe es keine Session, Angemeldete sähen die Fehlerseite wie abgemeldet. */
Route::fallback(fn () => abort(404));
