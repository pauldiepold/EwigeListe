<?php

namespace App\Http\Controllers;

use App\Http\Toast;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class StatisticsController extends Controller
{
    public function recalculate(): RedirectResponse
    {
        Artisan::call('stats:recalculate');
        Toast::success('Die Statistiken werden im Hintergrund neu berechnet.');

        return back();
    }
}
