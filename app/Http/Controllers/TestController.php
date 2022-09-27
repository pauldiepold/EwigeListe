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

    public function testAI()
    {
        $path = dirname(__FILE__) . '/../../Live/ai/nico_ki.py';
        $json = '{"computer_player_id":1,"starting_player_id":0,"played_cards":["ct"],"computer_player_hand":["sq","hq","dq","da","dt","dk","dn","sa","st","st","ca","ct"]}';
        $output = shell_exec('python3 ' . $path . ' -json ' . json_encode($json) . ' 2>&1');

        return $output;
    }
}
