<?php

namespace App\Http\Controllers;

use App\Models\BooksItem;
use App\Models\CdDvdItem;
use App\Services\CdsServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CdsController extends Controller
{
    private $cdsServices;

    public function __construct()
    {
        $this->middleware('deviceCheck');
        $this->cdsServices = new CdsServices();
    }

    /**
     * ゲーム情報取得
     *
     * @param Request $request
     * @return void
     */
    public function getCdInfo(Request $request)
    {
        $user_id = $request->input('user_id');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $released_status = $request->input('released_status');
        $search_word = $request->input('search_word');
        $genre = $request->input('genre');
        $genre_detail = $request->input('genre_detail');

        // db操作
        list($cds, $cds_count) = $this->cdsServices->getCds(
            $genre,
            $released_status,
            $limit,
            $offset,
            $genre_detail
        );

        logger(count($cds));
        logger($cds);

        foreach ($cds as $index => $cd) {
            // 日付フォーマット
            $cds[$index]['release_date'] = \Common::formatSalesDate($cd['release_date']);
        }

        return response()->json([
            'cd'        => $cds,
            'cds_count' => $cds_count
        ], 200);
    }


}
