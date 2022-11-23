<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\DeviceInfoRequest;
use App\Models\Notice;
use App\Services\GamesServices;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    private $gamesServices;

    public function __construct()
    {
        $this->middleware('deviceCheck');
        $this->gamesServices = new GamesServices();
    }

    // お知らせ取得
    public function getNoticeList(DeviceInfoRequest $request)
    {
        $notice = Notice::select("id", 'title', 'contents', 'created_at')
            ->get()
            ->toArray();

        $notice = array_map(function($value) {
            return [
                'id' => $value["id"],
                'title' => $value["title"],
                'contents' => $value["contents"],
                'created_at' => $this->gamesServices->formatSlashDate($value["created_at"])
            ];
        }, $notice);

        return response()->json([
            'message'   => 'success',
            'data'     => $notice,
        ], 200);

    }



}
