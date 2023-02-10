<?php

namespace App\Http\Controllers;

use App\Models\BooksItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function __construct()
    {
        $this->middleware('deviceCheck');
    }

    /**
     * 本の情報取得
     *
     * @param Request $request
     * @return void
     */
    public function getBooksInfo(Request $request)
    {
        $user_id = $request->input('user_id');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $is_released = $request->input('is_released');
        $search_word = $request->input('search_word');
        $size = $request->input('size');

        $today = Carbon::today();
        $today_format = $today->format('Ymd');

        // 本の種類
        $books = BooksItem::where('size', $size);

        // 発売日
        if ($is_released == 1) {
            // 発売前
            $books->where('sales_date', '<=', $today_format);   // 今日以前に発売されたゲームを取得
            $books->orderBy('sales_date', 'desc');
        } else if ($is_released == 2) {
            // 発売後
            $books->where('sales_date', '>', $today_format);   // 今日以降に発売されるゲームを取得
            $books->orderBy('sales_date', 'asc');
        }

        // 対象の総数を取得するために、limit・offsetする前にコピーする
        $book_copy = clone $books;
        $book_count = count($book_copy->get());

        $books->limit($limit)->offset($offset);
        $books = $books->get()->toArray();

        return response()->json([
            'books'         => $books,
            'book_count'    => $book_count
        ], 200);


    }


}
