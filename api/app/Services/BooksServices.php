<?php

namespace App\Services;

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
        string $genre,
        string $genre_detail,
        int $released_status,
        int $limit,
        int $offset,
    )
    {
        $books = BooksItem::query();

        // ジャンル
        $books
            ->where('genre', $genre)
            ->where('genre_detail', $genre_detail);

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

}



?>
