<?php

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
Route::get('/test', 'TestController@test');

Route::get('/', 'PlayerController@index');

Route::resource('/players','PlayerController');

Route::resource('/rounds','RoundController');
Route::get('/rounds/create/{numberOfPlayers?}', 'RoundController@create');

Route::resource('/games','GameController')->only([
    'destroy'
]);
Route::get('/rounds/{round}/game/create', 'GameController@create');
Route::post('/rounds/{round}/game', 'GameController@store');
Route::get('/games/delete/{round}', 'GameController@showDelete');

Route::get('/profiles/updateAll', 'ProfileController@updateAll');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
