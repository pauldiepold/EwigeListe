<?php

namespace App\Http\Controllers;

use App\Events\TestEvent;
use App\Live\Deck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{

    public function mail()
    {
        Mail::raw('It works!', function ($message) {
            $message->to('mail@pauldiepold.de')
                ->subject('Hello There');
        });

        return route('home');
    }

    public function test()
    {
        $deck = new Deck();

        $deck = $deck->deck->get(0);

        $test = collect(['isRe' => 10, 'hand' => $deck, 'ansage' => 20, 'absage' => 40]);

        $test->put('isRe', false);


        return view('test.index');
    }

    public function client()
    {
        return view('test.client');
    }
}
