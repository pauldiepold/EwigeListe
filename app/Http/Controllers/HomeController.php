<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Player;
use App\Game;
use App\Round;
use App\Comment;

class HomeController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        $comments = Comment::where('created_at', '>=', Carbon::now()->subDays(2))->latest()->paginate(5);

        $group = Group::find(1);

        $currentRounds = Round::where('updated_at', '>=', Carbon::now()->subDay()->toDateTimeString())
            ->with(['players', 'games'])
            ->has('games')
            ->latest('updated_at')
            ->get();

        return view('home.home', compact('group', 'currentRounds', 'comments'));
    }
}
