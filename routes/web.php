<?php

use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('players/{orderBy?}/{order?}', 'PlayerController@index');
Route::get('profiles/{player}', 'ProfileController@show');
Route::resource('/players','PlayerController');

Route::get('/rounds/current', function() {
    $lastRound = Auth::user()->player->games()->latest()->first()->round;
    return redirect('/rounds/' . $lastRound->id);
});
Route::resource('/rounds','RoundController');
Route::get('/rounds/create/{numberOfPlayers?}', 'RoundController@create');

Route::resource('/games','GameController')->only([
    'destroy', 'update'
]);
Route::get('/rounds/{round}/game/create', 'GameController@create');
Route::post('/rounds/{round}/game', 'GameController@store');
Route::patch('/rounds/{round}/game', 'GameController@update');
Route::get('/games/delete/{round}', 'GameController@showDelete');

Route::get('/profiles/updateAll', 'ProfileController@updateAll');

Route::get('autocomplete', 'SearchController@autocomplete')->name('autocomplete');

Auth::routes();

Route::get('/test', 'TestController@test');
