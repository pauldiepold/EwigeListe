<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use App\live\Board;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function test()
    {
        $board = new Board();

        dd($board->getDeck());

        return view('test.index');
    }

    public function client()
    {
        return view('test.client');
    }
}
