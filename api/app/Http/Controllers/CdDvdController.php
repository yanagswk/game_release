<?php

namespace App\Http\Controllers;

use App\Models\BooksItem;
use App\Models\CdDvdItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CdDvdController extends Controller
{
    public function __construct()
    {
        $this->middleware('deviceCheck');
    }

    /**
     * cd/dvdの情報取得
     *
     * @param Request $request
     * @return void
     */
    public function getCdDvdInfo(Request $request)
    {
        $user_id = $request->input('user_id');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $is_released = $request->input('is_released');
        $search_word = $request->input('search_word');
        $type = $request->input('type');

        $today = Carbon::today();
        $today_format = $today->format('Ymd');

        // 本の種類
        $cddvd = CdDvdItem::where('type', $type);   // 1:cd 2:dvd

        // 発売日
        if ($is_released == 1) {
            // 発売前
            $cddvd->where('sales_date', '<=', $today_format);   // 今日以前に発売されたゲームを取得
            $cddvd->orderBy('sales_date', 'desc');
        } else if ($is_released == 2) {
            // 発売後
            $cddvd->where('sales_date', '>', $today_format);   // 今日以降に発売されるゲームを取得
            $cddvd->orderBy('sales_date', 'asc');
        }

        // 対象の総数を取得するために、limit・offsetする前にコピーする
        $cddvd_copy = clone $cddvd;
        $cddvd_count = count($cddvd_copy->get());

        $cddvd->limit($limit)->offset($offset);
        $cddvd = $cddvd->get()->toArray();

        return response()->json([
            'cddvd'         => $cddvd,
            'cddvd_count'   => $cddvd_count
        ], 200);


    }


}
