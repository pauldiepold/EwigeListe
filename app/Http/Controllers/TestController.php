<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function test()
    {
        $date1 = Carbon::now();
        $date2 = Carbon::now()->addDays(-5);
        ddd($date2->diffInDays($date1, false));

        return view('test.index');
    }
}
