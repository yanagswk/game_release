<?php

namespace App\Services;

use App\Models\CdsItem;
use App\Models\Games;
use App\Models\GamesItem;
use Carbon\Carbon;

class CdsServices
{
    /**
     * cdの情報を取得
     *
     * @param string $genre             ジャンル
     * @param string $genre_detail      ジャンル詳細
     * @param integer $released_status  リリースステータス
     * @return array
     */
    public function getCds(
        string $genre,
        int $released_status,
        int $limit,
        int $offset,
        ?string $genre_detail = null,
    )
    {
        $games = CdsItem::query();

        // ジャンル(ハードウェア)
        $games->where('genre', $genre);

        // ゲーム詳細ジャンル
        if (!is_null($genre_detail)) {
            $games->where('genre_detail', $genre_detail);
        }

        // 発売日
        if ($released_status == CdsItem::BEFORE_RELEASE) {
            // 発売前 今日以前に発売されたゲームを取得
            $games
                ->where('release_date', '<=', \Common::getToday())
                ->orderBy('release_date', 'desc');
        } else if ($released_status == CdsItem::AFTER_RELEASE) {
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
