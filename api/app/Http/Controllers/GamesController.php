<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\DeviceInfoRequest;
use App\Http\Requests\Games\AddFavoriteGameRequest;
use App\Http\Requests\Games\ReleaseGamesRequest;
use App\Models\FavoriteGames;
use App\Models\Games;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Services\GamesServices;


class GamesController extends Controller
{
    private $gamesServices;

    public function __construct()
    {
        $this->middleware('deviceCheck', ['except' => [
            'getBeforeReleaseGames',
            'getReleasedGames'
        ]]);

        $this->gamesServices = new GamesServices();
    }

    /**
     * 発売前のゲーム一覧取得
     *
     * @param ReleaseGamesRequest $request
     * @return void
     */
    public function getBeforeReleaseGames(ReleaseGamesRequest $request)
    {
        // \Log::debug('');
        // \Log::debug('----------------------------デバック開始----------------------------');

        $hardware = $request->input('hardware');
        $limit = $request->input('limit');
        $offset = $request->input('offset');

        $today = Carbon::today();
        $today_format = $today->format('Ymd');

        $games = Games::where('sales_date', '>=', $today_format)     // 今日以降に発売されたゲームを取得
            ->orderBy('sales_date', 'asc')
            ->limit($limit)
            ->offset($offset);

        if ($hardware !== 'All') {
            $games->where('hardware', $hardware);
        }

        $games = $games->get();

        // 日付フォーマット
        $games = $this->gamesServices->formatSalesDate($games);

        return response()->json([
            'message'   => 'success',
            'games'     => $games
        ], 200);
    }


    /**
     * 発売済みのゲーム一覧取得
     *
     * @param ReleaseGamesRequest $request
     * @return void
     */
    public function getReleasedGames(ReleaseGamesRequest $request)
    {
        $hardware = $request->input('hardware');
        $limit = $request->input('limit');
        $offset = $request->input('offset');

        $today = Carbon::today();
        $today_format = $today->format('Ymd');

        $games = Games::where('sales_date', '<=', $today_format)     // 今日以前に発売されたゲームを取得
            ->orderBy('sales_date', 'desc')
            ->limit($limit)
            ->offset($offset);

        // ハードウェアが選択されている場合
        if ($hardware !== 'All' ) {
            $games->where('hardware', $hardware);
        }

        $games = $games->get();

        // 日付フォーマット
        $games = $this->gamesServices->formatSalesDate($games);

        return response()->json([
            'message'   => 'success',
            'games'     => $games
        ], 200);
    }


    /**
     * ゲームの詳細情報
     *
     * @param AddFavoriteGameRequest $request
     * @return void
     */
    public function getGamesDetail(AddFavoriteGameRequest $request)
    {
        $device_id = $request->input('device_id');
        $game_id = $request->input('game_id');

        // ユーザーid取得
        $user_id = UserInfo::where('device_id', $device_id)
            ->pluck('id')
            ->first();

        // ゲームidが存在するか
        $games = Games::where('id', $game_id)->first();
            // リレーション先のfavorite_gamesテーブルを絞り込む
            // ->with(['favorite' => function ($query) use ($user_id) {
            //     $query->where('user_id', $user_id);
            // }])
            // リレーション先のavorite_gamesテーブルで、Gamesテーブルを絞り込む場合
            // ->whereHas('favorite', function ($query) use ($user_id) {
            //     $query->where('user_id', $user_id);
            // });
        if (!$games) {
            return response()->json([
                'message'   => 'game does not exist'
            ], 400);
        }

        // ゲームがお気に入り済みか
        $favorite_game = FavoriteGames::where([
            ['user_id', $user_id],
            ['games_id', $game_id],
        ])->first();

        // nullの場合と無効の場合はfalseへ
        if (!$favorite_game || $favorite_game->is_disabled) {
            $games['is_favorite'] = false;
        } else {
            $games['is_favorite'] = true;
        }

        return response()->json([
            'message'   => 'success',
            'data'      => $games
        ], 200);
    }


    /**
     * お気に入りのゲーム一覧取得
     *
     * @param Request $request
     * @return void
     */
    public function getFavoriteGameList(DeviceInfoRequest $request)
    {
        $device_id = $request->input('device_id');

        // ユーザーid取得
        $user_id = UserInfo::userId($device_id);

        // お気に入りゲーム一覧取得
        $favorite_games = FavoriteGames::with('games')
            ->active()
            ->where('user_id', $user_id)
            ->orderBy('updated_at', 'DESC')
            ->get()
            ->toArray();

        // ゲーム情報のみにする
        $games_info = array_map(function($game) {
            return $game['games'];
        }, $favorite_games);

        return response()->json([
            'message'   => 'success',
            'data'      => $games_info
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
