<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LogOutHiddenPlayers;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withBroadcasting(__DIR__.'/../routes/channels.php')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            LogOutHiddenPlayers::class,
        ]);

        $middleware->alias([
            'admin' => Admin::class,
        ]);

        $middleware->redirectUsersTo('/home');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
