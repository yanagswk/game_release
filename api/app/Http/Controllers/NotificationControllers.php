<?php

namespace App\Http\Controllers;

use App\Models\Games;
use App\Models\Notice;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationControllers extends Controller
{

    public function __construct()
    {
        $this->middleware('deviceCheck');
    }

    /**
     * 通知登録
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        $user_id = $request->input('user_id');
        $game_id = $request->input('game_id');

        // データが存在しなかったら追加、あったらis_disabledを更新
        $notification = Notification::updateOrCreate(
            ['game_id' => $game_id, 'user_id' => $user_id],
            ['is_disabled' => 0]
        );

        return response()->json([
            'message'   => 'success',
            'data'      => [
                'notification_id' => $notification->id
            ]
        ], 200);
    }


    /**
     * 通知キャンセル
     *
     * @param Request $request
     * @return void
     */
    public function cancel(Request $request)
    {
        $user_id = $request->input('user_id');
        $game_id = $request->input('game_id');
        $notification_id = $request->input('notification_id');

        // ゲームidが存在するか
        // TODO: 処理かぶり 共通化する
        $games = Games::where('id', $game_id);
        if (!$games->exists()) {
            return response()->json([
                'message'   => 'game does not exist'
            ], 400);
        }

        // 通知idが存在するか
        $notification = Notification::where('id', $notification_id);
        if (!$notification->exists()) {
            return response()->json([
                'message'   => 'notification does not exist'
            ], 400);
        }

        // is_disabledをtrue(無効へ)
        Notification::where([
            ['user_id', $user_id],
            ['game_id', $game_id],
        ])->update([
            'is_disabled' => true
        ]);

        return response()->json([
            'message'   => 'success',
        ], 200);
    }



}
