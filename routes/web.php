<?php

use Illuminate\Support\Facades\Auth;

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
Route::get('updateProfil', function ()
{
    $profiles = App\Profile::all();
    foreach ($profiles as $profile)
    {
        $profile->updateProfile();
    }

    return redirect('/players');
});


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
Route::delete('/rounds/{round}', 'RoundController@destroy')->middleware('auth')->name('rounds.destroy');


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
//Route::patch('/games/{game}', 'GameController@update')->middleware('auth');
//Route::delete('/games/{game}', 'GameController@destroy')->middleware('auth');


/* *********** Charts ************** */
Route::get('/charts/round/{round}/', 'ChartController@roundChart')->middleware('auth');
Route::get('/charts/profile/{profile}/', 'ChartController@profileChart')->middleware('auth');
Route::get('/charts/home', 'ChartController@homeChart');


/* *********** Comments ************** */
Route::post('/comments', 'CommentController@store')->middleware('auth');
Route::delete('/comments/{comment}', 'CommentController@destroy')->middleware('auth');


/* *********** Sonstiges ************** */
Route::view('/datenschutz/', 'sonstiges.datenschutz');
Route::view('/impressum/', 'sonstiges.impressum');
Route::view('/regeln/', 'sonstiges.regeln');

Route::get('autocomplete', 'SearchController@autocomplete')->name('autocomplete')->middleware('auth');

Route::get('/test', 'TestController@test')->middleware('auth');

Route::get('/report', 'ReportController@report')->middleware(['auth', 'admin']);


