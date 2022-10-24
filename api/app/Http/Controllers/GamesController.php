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

    public function __construct()
    {
        $this->middleware('deviceCheck', ['except' => ['getBeforeReleaseGames']]);
    }

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

        // ユーザーid取得
        $user_id = UserInfo::where('device_id', $device_id)
            ->pluck('id')
            ->first();

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
            $game = $favorite_games->first();
            // 既にお気に入り済みの場合
            if (!$game->is_disabled) {
                return response()->json([
                    'message'   => 'already favorited'
                ], 400);
            }

            $game->update(['is_disabled' => false]);
            return response()->json([
                'message'   => 'success',
            ], 200);
        }

        // お気に入り新規登録
        FavoriteGames::create([
            'user_id' => $user_id,
            'games_id' => $game_id,
        ]);

        return response()->json([
            'message'   => 'success',
        ], 200);
    }


    /**
     * ゲームのお気に入り解除
     *
     * @param AddFavoriteGameRequest $request
     * @return void
     */
    public function removeFavoriteGame(AddFavoriteGameRequest $request)
    {
        $device_id = $request->input('device_id');
        $game_id = $request->input('game_id');

        // ユーザーid取得
        $user_id = UserInfo::where('device_id', $device_id)
            ->pluck('id')
            ->first();

        // ゲームidが存在するか
        $games = Games::where('id', $game_id);
        if (!$games->exists()) {
            return response()->json([
                'message'   => 'game does not exist'
            ], 400);
        }

        // お気に入りテーブルに存在するか
        $favorite_games = FavoriteGames::where([
            ['user_id', $user_id],
            ['games_id', $game_id],
            ['is_disabled', false]
        ]);
        if (!$favorite_games->exists()) {
            return response()->json([
                'message'   => 'Game not favorited'
            ], 400);
        }
        // is_disabledをtrue(無効へ)
        FavoriteGames::where([
            ['user_id', $user_id],
            ['games_id', $game_id],
        ])->update([
            'is_disabled' => true
        ]);

        return response()->json([
            'message'   => 'success',
        ], 200);
    }
}
