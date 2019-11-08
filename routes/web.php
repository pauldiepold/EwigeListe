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
        $lastGame = Auth::user()->player->games()->latest()->first();
        if ($lastGame)
        {
            $lastRound = Auth::user()->player->games()->latest()->first()->round;

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


Route::get('/umzug1', function ()
{
    $date = Carbon\Carbon::createMidnightDate(2018, 03, 25);

    $group = factory('App\Group')->create([
        'created_by' => App\Player::find(1),
        'name' => 'Ewige Liste',
        'created_at' => $date,
        'updated_at' => $date,
    ]);

    $players = App\Player::all();

    $group->players()->saveMany($players);

    $players->each(function ($player, $key)
    {
        $date = $player->created_at;

        DB::table('profiles')
            ->where('player_id', $player->id)
            ->update(['created_at' => $date, 'updated_at' => $date]);
    });
});

Route::get('/umzug2', function ()
{
    $group = App\Group::find(1);

    $rounds = App\Round::all();

    $group->rounds()->saveMany($rounds);

    $rounds->each(function ($round, $key)
    {
        $date = $round->created_at;

        DB::table('group_round')
            ->where('round_id', $round->id)
            ->update(['created_at' => $date, 'updated_at' => $date]);
    });
});

Route::get('/umzug3', function ()
{
    $group = App\Group::find(1);

    $players = App\Player::all();

    $players->each(function ($player, $key)
    {
        $player->calculate();
    });

    $group->calculate();
    $group->calculateBadges();
});


