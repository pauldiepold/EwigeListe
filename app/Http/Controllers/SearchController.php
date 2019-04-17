<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Player;

class SearchController extends Controller {

    public function autocomplete(Request $request)
    {
        $data = Player::select(['surname', 'name'])
            ->where('surname', 'LIKE', "%{$request->input('query')}%")
            ->get();

        $modifiedData = collect();
        foreach ($data as $item)
        {
            $modifiedData->push($item->surname . ' ' . $item->name);
        }

        return $modifiedData;
    }
}
