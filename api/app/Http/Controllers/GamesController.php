<?php

namespace App\Http\Controllers;

use App\Http\Requests\Games\AddFavoriteGameRequest;
use App\Http\Requests\Games\BeforeReleaseGamesRequest;
use App\Models\FavoriteGames;
use App\Models\Games;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    /**
     * 発売前のゲーム一覧取得
     *
     * @param BeforeReleaseGamesRequest $request
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
            'message'   => 'success',
            'games'     => $games
        ], 200);
    }


    /**
     * ゲームをお気に入り登録する
     *
     * @param Request $request
     * @return void
     */
    public function addFavoriteGame(AddFavoriteGameRequest $request)
    {
        $device_id = $request->input('device_id');
        $game_id = $request->input('game_id');

        // デバイスIDが存在するか
        $user_id = UserInfo::where('device_id', $device_id)
            ->pluck('id')
            ->first();
        if (!$user_id) {
            return response()->json([
                'message'   => 'user not found'
            ], 400);
        }

        // ゲームidが存在するか
        $games = Games::where('id', $game_id);
        if (!$games->exists()) {
            return response()->json([
                'message'   => 'game does not exist'
            ], 400);
        }

        // お気に入りテーブルに存在しないか
        $favorite_games = FavoriteGames::where([
            ['user_id', $user_id],
            ['games_id', $game_id],
        ]);
        if ($favorite_games->exists()) {
            return response()->json([
                'message'   => 'already favorited'
            ], 400);
        }

        // お気に入り登録
        FavoriteGames::create([
            'user_id' => $user_id,
            'games_id' => $game_id,
        ]);

        \Log::debug('成功');

        return response()->json([
            'message'   => 'success',
        ], 200);
    }
}
