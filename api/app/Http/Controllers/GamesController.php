<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\DeviceInfoRequest;
use App\Http\Requests\Games\AddFavoriteGameRequest;
use App\Http\Requests\Games\ReleaseGamesRequest;
use App\Library\GameLibrary;
use App\Models\FavoriteGames;
use App\Models\GameImage;
use App\Models\Games;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\GamesServices;
use Illuminate\Support\Facades\Log;

class GamesController extends Controller
{
    private $gamesServices;

    public function __construct()
    {
        $this->middleware('deviceCheck', ['except' => [
            // 'getBeforeReleaseGames',
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
    public function getGamesInfo(ReleaseGamesRequest $request)
    {

        // $hoge = \GameLibrary::fuga();

        $user_id = $request->input('user_id');
        $hardware = $request->input('hardware');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $is_released = $request->input('is_released');
        $search_word = $request->input('search_word');

        $today = Carbon::today();
        $today_format = $today->format('Ymd');

        $games = Games::with([
            'favorite' => function ($query) use ($user_id) {
                // リレーション先をユーザーで絞り込む
                $query->where('user_id', $user_id);
                $query->where('is_disabled', false);
            },
            'notification' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
                $query->where('is_disabled', false);
            },
            'game_image' => function ($query) {
                $query->where('image_type', GameImage::MAIN_IMG);   // メイン画像
            }
        ]);

        if ($hardware !== 'All') {
            $games->where('hardware', $hardware);
        }

        // 検索ワードを指定している場合は、発売前・発売後関係なしに全期間
        if ($search_word) {
            $games->where('title','like','%'.$search_word.'%');
        } else if ($is_released) {
            // 発売前
            $games->where('sales_date', '<=', $today_format);   // 今日以前に発売されたゲームを取得
            $games->orderBy('sales_date', 'desc');
        } else {
            // 発売後
            $games->where('sales_date', '>', $today_format);   // 今日以降に発売されたゲームを取得
            $games->orderBy('sales_date', 'asc');
        }

        // 対象の総数を取得するために、limit・offsetする前にコピーする
        $game_copy = clone $games;
        $game_count = count($game_copy->get());

        $games->limit($limit)->offset($offset);
        $games = $games->get()->toArray();

        foreach ($games as $index => $game) {
            if (count($game['game_image'])) {
                $main_img_url = $game['game_image'][0]['img_url'];
                // http://localhost/storage/img/1288/main_img.jpg
                $full_url = "http://localhost/storage/img/{$main_img_url}";
            } else {
                $full_url = "";
            }

            // 日付フォーマット
            $games[$index]['sales_date'] = $this->gamesServices->formatSalesDate($game['sales_date']);
            // お気に入りにしているか(空の場合はfalse)
            $games[$index]['is_favorite'] = !empty($game['favorite']) ? true : false;
            // 通知登録しているか(nullの場合はfalse)
            $games[$index]['is_notification'] = !is_null($game['notification']) ? true : false;
            // 通知id
            $games[$index]['notification_id'] = !is_null($game['notification']) ? $game['notification']['id'] : null;
            // メイン画像
            $games[$index]['main_img'] = $full_url;
            // お気に入りのリレーションを削除
            unset($games[$index]['favorite']);
            unset($games[$index]['notification']);
            unset($games[$index]['game_image']);
        }
        return response()->json([
            'message'       => 'success',
            'games'         => $games,
            'game_count'    => $game_count
        ], 200);
    }


    /**
     * ゲーム検索
     *
     * @param ReleaseGamesRequest $request
     * @return void
     */
    public function getSearchGames(Request $request)
    {
        $user_id = $request->input('user_id');
        $search_word = $request->input('search_word');
        $limit = $request->input('limit');
        $offset = $request->input('offset');

        $games = Games::with([
            'favorite' => function ($query) use ($user_id) {
                // リレーション先をユーザーで絞り込む
                $query->where('user_id', $user_id);
                $query->where('is_disabled', false);
            },
            'notification' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
                $query->where('is_disabled', false);
            },
            'game_image' => function ($query) {
                $query->where('image_type', GameImage::MAIN_IMG);   // メイン画像
            }
            ])
            ->where('title','like','%'.$search_word.'%')
            ->orderBy('sales_date', 'desc');

        // 対象の総数を取得するために、limit・offsetする前にコピーする
        $game_copy = clone $games;
        $game_count = count($game_copy->get());

        $games->limit($limit)->offset($offset);
        $games = $games->get()->toArray();


        foreach ($games as $index => $game) {
            if (count($game['game_image'])) {
                $main_img_url = $game['game_image'][0]['img_url'];
                // http://localhost/storage/img/1288/main_img.jpg
                $full_url = "http://localhost/storage/img/{$main_img_url}";
            } else {
                $full_url = "";
            }

            // 日付フォーマット
            $games[$index]['sales_date'] = $this->gamesServices->formatSalesDate($game['sales_date']);
            // お気に入りにしているか(空の場合はfalse)
            $games[$index]['is_favorite'] = !empty($game['favorite']) ? true : false;
            // 通知登録しているか(nullの場合はfalse)
            $games[$index]['is_notification'] = !is_null($game['notification']) ? true : false;
            // 通知id
            $games[$index]['notification_id'] = !is_null($game['notification']) ? $game['notification']['id'] : null;
            // メイン画像
            $games[$index]['main_img'] = $full_url;
            // お気に入りのリレーションを削除
            unset($games[$index]['favorite']);
            unset($games[$index]['notification']);
            unset($games[$index]['game_image']);
        }



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
        $user_id = $request->input('user_id');
        $game_id = $request->input('game_id');

        // ゲームidが存在するか
        $game = Games::where('id', $game_id)->first();
            // リレーション先のfavorite_gamesテーブルを絞り込む
            // ->with(['favorite' => function ($query) use ($user_id) {
            //     $query->where('user_id', $user_id);
            // }])
            // リレーション先のavorite_gamesテーブルで、Gamesテーブルを絞り込む場合
            // ->whereHas('favorite', function ($query) use ($user_id) {
            //     $query->where('user_id', $user_id);
            // });
        if (!$game) {
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
            $game['is_favorite'] = false;
        } else {
            $game['is_favorite'] = true;
        }

        // 日付フォーマット
        $game['sales_date'] = $this->gamesServices->formatSalesDate($game['sales_date']);

        return response()->json([
            'message'   => 'success',
            'data'      => $game
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
        $user_id = $request->input('user_id');

        // お気に入りゲーム一覧取得
        // $favorite_games = FavoriteGames::with('games') // TODO: games.sales_dateで並べ替えしたい
        // TODO: games.sales_dateで並べ替えしたい
        $favorite_games = FavoriteGames::with([
            'games' => function ($query) use ($user_id) {
                $query->with([
                    'notification' => function ($query2) use ($user_id) {
                        $query2->where('user_id', $user_id);
                        $query2->where('is_disabled', false);
                    },
                    'game_image' => function ($query) {
                        $query->where('image_type', GameImage::MAIN_IMG);   // メイン画像
                    }
                ]);
            },
            ])
            ->active()
            ->where('user_id', $user_id)
            ->get()
            ->toArray();

        // ゲーム情報のみにする
        $games_info = array_map(function($game) {
            return $game['games'];
        }, $favorite_games);

        $sales_date_list = array_column($games_info, 'sales_date');
        array_multisort($sales_date_list, SORT_DESC, $games_info);

        foreach ($games_info as $index => $game) {
            if (count($game['game_image'])) {
                $main_img_url = $game['game_image'][0]['img_url'];
                // http://localhost/storage/img/1288/main_img.jpg
                $full_url = "http://localhost/storage/img/{$main_img_url}";
            } else {
                $full_url = "";
            }

            // 日付フォーマット
            $games_info[$index]['sales_date'] = $this->gamesServices->formatSalesDate($game['sales_date']);
            // お気に入り
            $games_info[$index]['is_favorite'] = true;
            // 通知設定
            $games_info[$index]['is_notification'] = !is_null($game['notification']) ? true : false;
            // 通知id
            $games_info[$index]['notification_id'] = !is_null($game['notification']) ? $game['notification']['id'] : null;
            // メイン画像
            $games_info[$index]['main_img'] = $full_url;

            unset($games_info[$index]['notification']);
            unset($games_info[$index]['game_image']);
        }

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
        $user_id = $request->input('user_id');
        $game_id = $request->input('game_id');

        // ゲームidが存在するか
        $games = Games::where('id', $game_id);
        if (!$games->exists()) {
            return response()->json([
                'message'   => 'game does not exist'
            ], 400);
        }

        // お気に入りテーブルに存在しないか
        // TODO: 「存在しなければ登録、存在すれば更新」のメソッド使いたい　updateOrCreateかな？？
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
     * @param $request
     * @return void
     */
    public function removeFavoriteGame(AddFavoriteGameRequest $request)
    {
        $user_id = $request->input('user_id');
        $game_id = $request->input('game_id');

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
