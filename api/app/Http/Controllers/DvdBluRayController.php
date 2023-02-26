<?php

namespace App\Http\Controllers;

use App\Models\BooksItem;
use App\Models\CdDvdItem;
use App\Services\CdsServices;
use App\Services\DvdBluRayServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DvdBluRayController extends Controller
{
    private $dvdBluRayServices;

    public function __construct()
    {
        $this->middleware('deviceCheck');
        $this->dvdBluRayServices = new DvdBluRayServices();
    }

    /**
     * ゲーム情報取得
     *
     * @param Request $request
     * @return void
     */
    public function getDvdBluRayInfo(Request $request)
    {
        $user_id = $request->input('user_id');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $released_status = $request->input('released_status');
        $search_word = $request->input('search_word');
        $genre = $request->input('genre');
        $genre_detail = $request->input('genre_detail');

        // db操作
        list($dvds, $dvd_count) = $this->dvdBluRayServices->getDvdBluRays(
            $genre,
            $released_status,
            $limit,
            $offset,
            $genre_detail
        );

        logger(count($dvds));
        logger($dvds);

        foreach ($dvds as $index => $dvd) {
            // 日付フォーマット
            $dvds[$index]['release_date'] = \Common::formatSalesDate($dvd['release_date']);
        }

        return response()->json([
            'dvd'       => $dvds,
            'dvd_count' => $dvd_count
        ], 200);
    }


}
