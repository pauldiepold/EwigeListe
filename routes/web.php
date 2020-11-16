<?php

use Illuminate\Support\Facades\Route;
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

Route::middleware('auth')->group(function ()
{
    /* *********** Users *********** */
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::patch('users/{user}/name', 'UserController@updateName')->name('users.updateName');
    Route::patch('users/{user}/mail', 'UserController@updateMail')->name('users.updateMail');
    Route::patch('users/{user}/password', 'UserController@updatePassword')->name('users.updatePassword');
    Route::patch('users/{user}/listen', 'UserController@updateListen')->name('users.updateListen');

    Route::get('mail', 'TestController@mail');

    /* *********** Players *********** */
    Route::get('profil/{player}/{group?}', 'PlayerController@show');
    Route::get('/players/calculate', 'PlayerController@calculateAll');
    Route::get('/players/calculate/{player}', 'PlayerController@calculate');


    /* *********** Rounds ************ */
    Route::get('/rundenarchiv/{group?}', 'RoundController@index')->name('rounds.index');
    Route::get('/runde/erstellen', 'RoundController@create')->name('rounds.create');
    Route::post('/rounds', 'RoundController@store')->name('rounds.store');
    Route::get('/rounds/current', 'RoundController@current')->name('rounds.current');
    Route::get('/runde/{round}', 'RoundController@show')->name('rounds.show');
    Route::patch('/rounds/{round}', 'RoundController@update')->name('rounds.update');
    Route::delete('/rounds/{round}', 'RoundController@destroy')->name('rounds.destroy');
    Route::patch('/rounds/dates/{round}', 'RoundController@changeDates')->name('rounds.changeDates');
    Route::get('/rounds/ajax/{group}/{player?}', 'RoundController@archiveTable')->name('rounds.archiveTable');


    /* *********** Groups ************** */
    Route::get('liste', 'GroupController@show')->name('ewigeListe');
    Route::get('/listen', 'GroupController@index')->name('groups.index');
    Route::get('/liste/erstellen', 'GroupController@create')->name('groups.create');
    Route::get('/liste/{group}', 'GroupController@show')->name('groups.show');
    Route::post('/groups', 'GroupController@store')->name('groups.store');

    Route::get('/listen/calculate', 'GroupController@calculateAll');
    Route::get('/liste/calculate/{group}', 'GroupController@calculate');
    Route::get('/liste/calculateBadges/{group}', 'GroupController@calculateBadges');

    Route::get('/liste/{group}/beitreten', 'GroupController@update')->name('groups.addPlayer');
    Route::get('/liste/{group}/verlassen', 'GroupController@leave')->name('groups.leave');
    Route::get('/liste/{group}/schließen/{close}', 'GroupController@close')->name('groups.close');


    /* *********** Charts ************** */
    Route::get('/charts/round/{round}/', 'ChartController@roundChart');
    Route::get('/charts/profile/{profile}/', 'ChartController@profileChart');


    /* *********** Comments ************** */
    Route::post('/comments', 'CommentController@store');
    Route::delete('/comments/{comment}', 'CommentController@destroy');


    /* *********** API ************** */
    Route::prefix('api')->group(function ()
    {
        Route::get('/rounds/{round}/fetchData', 'RoundController@fetchData');

        Route::post('/rounds/{round}/game', 'GameController@store');
        Route::delete('/games/{game}', 'GameController@destroy');

        Route::middleware('can:update,liveRound')->group(function ()
        {
            Route::post('live/{liveRound}/spielStarten', 'Live\RoundController@starteNeuesSpiel');
        });

        Route::middleware('can:update,liveGame')->group(function ()
        {
            Route::post('live/{liveGame}/kartenGeben', 'Live\GameController@kartenGeben');
            Route::post('live/{liveGame}/vorbehalt', 'Live\GameController@vorbehalt');
            Route::post('live/{liveGame}/armutAbgeben', 'Live\GameController@armutAbgeben');
            Route::post('live/{liveGame}/armutMitnehmen', 'Live\GameController@armutMitnehmen');
            Route::post('live/{liveGame}/armutZurueck', 'Live\GameController@armutZurueckgeben');
            Route::post('live/{liveGame}/karteSpielen', 'Live\GameController@karteSpielen');
            Route::post('live/{liveGame}/ansage', 'Live\GameController@ansage');
            Route::get('live/{liveGame}/reloadData', 'Live\GameController@reloadData');
        });

        Route::post('users/{user}/avatar', 'Api\UserAvatarController@store')->name('avatar');

    });

    /* *********** Admin ************** */
    Route::middleware('admin')->group(function ()
    {
        Route::get('/test', 'TestController@test');
        Route::get('/testClient', 'TestController@client');
        Route::get('/report', 'ReportController@report');
    });
});


/* *********** Charts ************** */
Route::get('/charts/home/{group}', 'ChartController@homeChart');

/* *********** Sonstiges ************** */
Route::view('/datenschutz/', 'sonstiges.datenschutz')->name('datenschutz');
Route::view('/impressum/', 'sonstiges.impressum')->name('impressum');
Route::view('/regeln/', 'sonstiges.regeln')->name('regeln');


/* *********** AUTH ************** */
Route::get('auth/redirect/{provider}', 'Auth\SocialiteController@redirect')->name('auth.socialite');
Route::get('callback/{provider}', 'Auth\SocialiteController@callback');
Route::get('login/social/{socialiteUser}', 'Auth\SocialiteController@showView')->name('auth.registerOrAttach');




