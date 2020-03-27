<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function ()
{
    /* *********** Users *********** */
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::patch('users/{user}/name', 'UserController@updateName')->name('users.updateName');
    Route::patch('users/{user}/mail', 'UserController@updateMail')->name('users.updateMail');
    Route::patch('users/{user}/password', 'UserController@updatePassword')->name('users.updatePassword');
    Route::patch('users/{user}/listen', 'UserController@updateListen')->name('users.updateListen');

    /* *********** Players *********** */
    Route::get('profil/{player}/{group?}', 'PlayerController@show');
    Route::get('/players/calculate', function ()
    {
        App\Player::all()->each(function ($player, $key)
        {
            $player->calculate();
        });

        return redirect('/liste/1');
    });

    Route::get('/players/calculate/{player}', function (App\Player $player)
    {
        $player->calculate();

        return redirect($player->path());
    });


    /* *********** Rounds ************ */
    Route::get('/rounds/current',
        function ()
        {
            $lastRound = auth()->user()->player->rounds()->latest()->first();
            if ($lastRound)
            {
                return redirect($lastRound->path());
            } else
            {
                return redirect()->route('rounds.create');
            }
        })->name('rounds.current');

    Route::get('/rundenarchiv/{group?}', 'RoundController@index')->name('rounds.index');
    Route::get('/runde/erstellen', 'RoundController@create')->name('rounds.create');
    Route::post('/rounds', 'RoundController@store')->name('rounds.store');
    Route::get('/runde/{round}', 'RoundController@show')->name('rounds.show');
    Route::patch('/rounds/{round}', 'RoundController@update')->name('rounds.update');
    Route::delete('/rounds/{round}', 'RoundController@destroy')->name('rounds.destroy');
    Route::patch('/rounds/dates/{round}', 'RoundController@changeDates')->name('rounds.changeDates');


    /* *********** Games ************** */
    Route::get('/rounds/{round}/game/create', 'GameController@create');
    Route::post('/rounds/{round}/game', 'GameController@store');
    Route::patch('/games/{game}', 'GameController@update');
    Route::delete('/games/{game}', 'GameController@destroy');


    /* *********** Groups ************** */
    Route::get('liste', 'GroupController@show')->name('ewigeListe');
    Route::get('/listen', 'GroupController@index')->name('groups.index');
    Route::get('/liste/erstellen', 'GroupController@create')->name('groups.create');
    Route::get('/liste/{group}', 'GroupController@show')->name('groups.show');
    Route::post('/groups', 'GroupController@store')->name('groups.store');
    Route::get('/liste/{group}/beitreten', 'GroupController@update')->name('groups.addPlayer');
    Route::get('/liste/{group}/verlassen', 'GroupController@leave')->name('groups.leave');
    Route::get('/liste/{group}/schließen/{close}', 'GroupController@close')->name('groups.close');

    Route::get('/listen/calculate', function ()
    {
        App\Group::all()->each(function ($group, $key)
        {
            $group->calculate();
        });

        return redirect('/listen');
    });

    Route::get('/liste/calculate/{group}', function (App\Group $group)
    {
        $group->calculate();

        return redirect($group->path() . '#statistiken');
    });

    Route::get('/liste/calculateBadges/{group}', function (App\Group $group)
    {
        $group->calculateBadges();

        return redirect($group->path() . '#abzeichen');
    });


    /* *********** Charts ************** */
    Route::get('/charts/round/{round}/', 'ChartController@roundChart');
    Route::get('/charts/profile/{profile}/', 'ChartController@profileChart');


    /* *********** Comments ************** */
    Route::post('/comments', 'CommentController@store');
    Route::delete('/comments/{comment}', 'CommentController@destroy');

    /* *********** API ************** */
    Route::post('api/users/{user}/avatar', 'Api\UserAvatarController@store')->name('avatar');
});



/* *********** Rounds ************** */
Route::get('/rounds/ajax/{group}/{player?}', 'RoundController@archiveTable');

/* *********** Charts ************** */
Route::get('/charts/home/{group}', 'ChartController@homeChart');


/* *********** Sonstiges ************** */
Route::view('/datenschutz/', 'sonstiges.datenschutz')->name('datenschutz');
Route::view('/impressum/', 'sonstiges.impressum')->name('impressum');
Route::view('/regeln/', 'sonstiges.regeln')->name('regeln');

Route::get('/test', 'TestController@test')->middleware('auth');
Route::get('/testClient', 'TestController@client')->middleware('auth');

Route::get('/report', 'ReportController@report')->middleware(['auth', 'admin']);


/* *********** AUTH ************** */
Route::get('auth/redirect/{provider}', 'Auth\SocialiteController@redirect')->name('auth.socialite');
Route::get('callback/{provider}', 'Auth\SocialiteController@callback');
Route::get('login/social/{socialiteUser}', 'Auth\SocialiteController@showView')->name('auth.registerOrAttach');




