<?php

namespace App\Http\Controllers;

use App\Models\GamesItem;
use Illuminate\Http\Request;

class VueController extends Controller
{
    public function index(Request $request)
    {

        // $games = GamesItem::query()->get()->toArray();
        $games = GamesItem::query()->find(22)->toArray()["title"];

        // dd($games);


        return view('vue', compact('games'));
    }
}
