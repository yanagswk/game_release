<?php

namespace App\Services;

use App\Models\Games;
use App\Models\GamesItem;
use Carbon\Carbon;

class GamesServices
{

    /**
     * 発売日のフォーマットを整える
     *
     * @return void
     */
    public function formatSalesDate($sales_date)
    {
        $date  = new Carbon($sales_date);
        $year = mb_substr($sales_date, 0, 4);
        $month = mb_substr($sales_date, 4, 2);

        if ($sales_date == '00000000') {
            // 「00000000」パターン
            return "発売時期不明";
        } else if (str_ends_with($sales_date, '0000')) {
            // 例)「20220000」パターン
            return "{$year}年中";
        } else if (str_ends_with($sales_date, '00')) {
            // 「20221200」
            return "{$year}年{$month}月中";
        } else {
            return $date->format('Y年m月d日');
        }
    }

    /**
     * yyyy/mm/ddのフォーマットにする
     */
    public function formatSlashDate($date)
    {
        $date  = new Carbon($date);
        return $date->format('Y/m/d');
    }


    /**
     * ゲームの情報を取得
     *
     * @param string $genre             ジャンル
     * @param string $genre_detail      ジャンル詳細
     * @param integer $released_status  リリースステータス
     * @return array
     */
    public function getGames(
        string $genre,
        int $released_status,
        int $limit,
        int $offset,
        ?string $genre_detail = null,
    )
    {
        $games = GamesItem::query();

        // ジャンル(ハードウェア)
        $games->where('genre', $genre);

        // ゲーム詳細ジャンル
        if (!is_null($genre_detail)) {
            $games->where('genre_detail', $genre_detail);
        }

        // 発売日
        if ($released_status == GamesItem::BEFORE_RELEASE) {
            // 発売前 今日以前に発売されたゲームを取得
            $games
                ->where('release_date', '<=', \Common::getToday())
                ->orderBy('release_date', 'desc');
        } else if ($released_status == GamesItem::AFTER_RELEASE) {
            // 発売後 今日以降に発売されるゲームを取得
            $games
                ->where('release_date', '>', \Common::getToday())
                ->orderBy('release_date', 'asc');
        }

        // 対象の総数を取得するために、limit・offsetする前にコピーする
        $book_copy = clone $games;
        $book_count = count($book_copy->get());
        $games->page($limit, $offset);

        $games->active();

        return [
            $games->get()->toArray(),
            $book_count
        ];
    }
}

?>
