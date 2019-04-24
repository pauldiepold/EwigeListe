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
Route::get(      '/',                                     'HomeController@index');
Route::get(      '/home',                                 'HomeController@index')->name('home');


/* *********** Players *********** */
Route::get(      'players/{orderBy?}/{order?}',           'PlayerController@index');
Route::get(      'profiles/update',                       'ProfileController@updateAll')  ->middleware('auth');
Route::get(      'profiles/{player}',                     'ProfileController@show')  ->middleware('auth');


/* *********** Rounds ************ */
Route::get(     '/rounds/current',
function() {
    $lastRound = Auth::user()->player->games()->latest()->first()->round;
    return redirect('/rounds/' . $lastRound->id);
})                                                                                   ->middleware('auth');

Route::get(     '/rounds',                                'RoundController@index')   ->middleware('auth');
Route::get(     '/rounds/create/{numberOfPlayers?}',      'RoundController@create')  ->middleware('auth')  ->where('numberOfPlayers', '[4-7]');
Route::post(    '/rounds',                                'RoundController@store')   ->middleware('auth');
Route::get(     '/rounds/{round}',                        'RoundController@show')    ->middleware('auth');


/* *********** Games ************** */
Route::get(      '/rounds/{round}/game/create',           'GameController@create')   ->middleware('auth');
Route::post(     '/rounds/{round}/game',                  'GameController@store')    ->middleware('auth');
Route::patch(    '/games/{game}',                         'GameController@update')   ->middleware('auth');
Route::delete(   '/games/{game}',                         'GameController@destroy')  ->middleware('auth');


/* *********** Invites ************ */
Route::get(      '/invites',                              'InviteController@index')  ->middleware('auth')     ->name('showInvites');
Route::post(     '/invites',                              'InviteController@store')  ->middleware('auth');
Route::get(      '/invites/deleteOld',                    'InviteController@destroyOld');
Route::delete(   '/invites/{invite}',                     'InviteController@destroy')->middleware('auth');


Route::get('autocomplete', 'SearchController@autocomplete')->name('autocomplete')    ->middleware('auth');

Route::get(      '/test',                                 'TestController@test')     ->middleware('auth');
