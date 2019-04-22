<?php

namespace App\Http\Controllers;

use App\Round;
use App\Player;
use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreRound;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class TestController extends Controller {

    public function test()
    {
        return view('test.index');
    }
}
