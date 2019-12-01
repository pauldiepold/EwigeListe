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


/* *********** Players *********** */
Route::get('liste', 'GroupController@show')->name('ewigeListe');
Route::get('profil/{player}/{group?}', 'PlayerController@show')->middleware('auth');

Route::get('/players/calculate', function ()
{
    App\Player::all()->each(function ($player, $key)
    {
        $player->calculate();
    });

    return redirect('/liste/1');
})->middleware('auth');

Route::get('/players/calculate/{player}', function (App\Player $player)
{
    $player->calculate();

    return redirect($player->path());
})->middleware('auth');


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
    })->middleware('auth')->name('rounds.current');

Route::get('/rundenarchiv/{group?}', 'RoundController@index')->middleware('auth')->name('rounds.index');
Route::get('/runde/erstellen', 'RoundController@create')->middleware('auth')->name('rounds.create');
Route::post('/rounds', 'RoundController@store')->middleware('auth')->name('rounds.store');
Route::get('/runde/{round}', 'RoundController@show')->middleware('auth')->name('rounds.show');
Route::patch('/rounds/{round}', 'RoundController@update')->middleware('auth')->name('rounds.update');
Route::delete('/rounds/{round}', 'RoundController@destroy')->middleware('auth')->name('rounds.destroy');
Route::get('/rounds/ajax/{group}/{player?}', 'RoundController@archiveTable');


/* *********** Games ************** */
Route::get('/rounds/{round}/game/create', 'GameController@create')->middleware('auth');
Route::post('/rounds/{round}/game', 'GameController@store')->middleware('auth');
Route::patch('/games/{game}', 'GameController@update')->middleware('auth');
Route::delete('/games/{game}', 'GameController@destroy')->middleware('auth');


/* *********** Groups ************** */
Route::get('/listen', 'GroupController@index')->middleware('auth')->name('groups.index');
Route::get('/liste/erstellen', 'GroupController@create')->middleware('auth')->name('groups.create');
Route::get('/liste/{group}', 'GroupController@show')->middleware('auth')->name('groups.show');
Route::post('/groups', 'GroupController@store')->middleware('auth')->name('groups.store');
Route::get('/liste/{group}/beitreten', 'GroupController@update')->middleware('auth')->name('groups.addPlayer');
Route::get('/liste/{group}/verlassen', 'GroupController@leave')->middleware('auth')->name('groups.leave');
Route::get('/liste/{group}/schließen/{close}', 'GroupController@close')->middleware('auth')->name('groups.close');

Route::get('/listen/calculate', function ()
{
    App\Group::all()->each(function ($group, $key)
    {
        $group->calculate();
    });

    return redirect('/listen');
})->middleware('auth');

Route::get('/liste/calculate/{group}', function (App\Group $group)
{
    $group->calculate();

    return redirect($group->path() . '#statistiken');
})->middleware('auth');

Route::get('/liste/calculateBadges/{group}', function (App\Group $group)
{
    $group->calculateBadges();

    return redirect($group->path() . '#abzeichen');
})->middleware('auth');


/* *********** Charts ************** */
Route::get('/charts/round/{round}/', 'ChartController@roundChart')->middleware('auth');
Route::get('/charts/profile/{profile}/', 'ChartController@profileChart')->middleware('auth');
Route::get('/charts/home/{group}', 'ChartController@homeChart');


/* *********** Comments ************** */
Route::post('/comments', 'CommentController@store')->middleware('auth');
Route::delete('/comments/{comment}', 'CommentController@destroy')->middleware('auth');


/* *********** Sonstiges ************** */
Route::view('/datenschutz/', 'sonstiges.datenschutz')->name('datenschutz');
Route::view('/impressum/', 'sonstiges.impressum')->name('impressum');
Route::view('/regeln/', 'sonstiges.regeln')->name('regeln');

Route::get('autocomplete', 'SearchController@autocomplete')->name('autocomplete')->middleware('auth');

Route::get('/test', 'TestController@test')->middleware('auth');

Route::get('/report', 'ReportController@report')->middleware(['auth', 'admin']);


/* *********** AUTH ************** */
Route::get('auth/redirect/{provider}', 'Auth\SocialiteController@redirect')->name('auth.socialite');
Route::get('callback/{provider}', 'Auth\SocialiteController@callback');
Route::get('login/social/{socialiteUser}', 'Auth\SocialiteController@showView')->name('auth.registerOrAttach');




