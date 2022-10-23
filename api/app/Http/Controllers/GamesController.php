<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeforeReleaseGamesRequest;
use App\Models\Games;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    /**
     * 発売前のゲーム一覧取得
     *
     * @param Request $request
     * @return void
     */
    public function getBeforeReleaseGames(BeforeReleaseGamesRequest $request)
    {
        // \Log::debug('');
        // \Log::debug('----------------------------デバック開始----------------------------');

        $hardware = $request->input('hardware');
        $limit = $request->input('limit');
        $offset = $request->input('offset');

        $today = Carbon::today();

        $games = Games::where('hardware', $hardware)
            ->whereDate('sales_date', '>=', $today)     // 今日以降に発売されたゲームを取得
            ->orderBy('sales_date', 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return response()->json([
            'games' => $games
        ], 200);

    }
}
