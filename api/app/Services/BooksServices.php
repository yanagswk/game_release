<?php

namespace App\Services;

use App\Models\BookSeriesMaster;
use App\Models\BooksItem;

class BooksServices
{

    /**
     * 本の情報を取得
     *
     * @param string $genre             ジャンル
     * @param string $genre_detail      ジャンル詳細
     * @param integer $released_status  リリースステータス
     * @return array
     */
    public function getBooks(
        int $limit,
        int $offset,
        ?string $genre=null,
        ?int $released_status =null,
        ?string $genre_detail=null,
        ?string $search_word=null,
    )
    {
        $books = BooksItem::query();
        // ジャンル
        // if (!is_null($genre)) {
        if ($genre != "選択しない") {
            $books->where('genre', $genre);
        }

        logger($genre_detail);
        logger($genre_detail === "全て");

        // ゲーム詳細ジャンル
        // if (!is_null($genre_detail) || $genre_detail !== "全て") {
        if ($genre_detail !== "全て") {
            $books->where('genre_detail', $genre_detail);
        }

        // 検索
        if (!is_null($search_word)) {
            $books->where('title', 'like', '%'.$search_word.'%');
        }

        // 発売日
        if ($released_status == BooksItem::BEFORE_RELEASE) {
            // 発売前 今日以前に発売されたゲームを取得
            $books
                ->where('release_date', '<=', \Common::getToday())
                ->orderBy('release_date', 'desc');
        } else if ($released_status == BooksItem::AFTER_RELEASE) {
            // 発売後 今日以降に発売されるゲームを取得
            $books
                ->where('release_date', '>', \Common::getToday())
                ->orderBy('release_date', 'asc');
        }

        // 対象の総数を取得するために、limit・offsetする前にコピーする
        $book_copy = clone $books;
        $book_count = count($book_copy->get());
        $books->page($limit, $offset);

        return [
            $books->get()->toArray(),
            $book_count
        ];
    }


    /**
     * シリーズを登録していないコンテンツを取得
     *
     * @return array
     */
    public function getUnregisteredSeriesContents()
    {
        $books = BooksItem::query()
            ->select('id', 'series', 'author')
            ->whereNotNull('series')                // シリーズが存在する
            ->where('is_series_checked', false)     // チェック済みでない
            ->get()->toArray();
        return $books;
    }


    /**
     * シリーズタイトルをマスターテーブルへ保存
     *
     * @return void
     */
    public function insertSeriesTitles(array $books)
    {
        BookSeriesMaster::query()->upsert(
            $books,
            ['series'],     // ユニークなカラム
            ['updated_at']  // update対象
        );
    }


    /**
     * シリーズチェックフラグをtrueにする
     *
     * @return void
     */
    public function updateSeriesChecked(array $id_list)
    {
        BooksItem::query()
            ->whereIn('id', $id_list)
            ->update([
                'is_series_checked' => true
            ]);
    }

}



?>
