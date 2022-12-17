<?php

namespace App\Http\Controllers;

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

        $notification = Notification::create([
            'user_id'   => $user_id,
            'game_id'   => $game_id,
        ]);

        return response()->json([
            'message'   => 'success',
            'data'      => [
                'notification_id' => $notification->id
            ]
        ], 200);
    }
}
