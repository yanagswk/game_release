<?php

namespace App\Http\Controllers;

use App\Http\Resources\BooksResource;
use App\Models\BooksItem;
use App\Services\BooksServices;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function PHPUnit\Framework\countOf;

class BooksController extends Controller
{
    private $booksServices;

    public function __construct()
    {
        $this->middleware('deviceCheck');
        $this->booksServices = new BooksServices();
    }

    /**
     * 本の情報取得
     *
     * @param Request $request
     * @return void
     */
    public function list(Request $request)
    {
        logger($request->all());

        $user_id = $request->input('user_id');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $released_status = $request->input('released_status');
        $search_word = $request->input('search_word');
        $genre = $request->input('genre');
        $genre_detail = $request->input('genre_detail');

        // db操作
        list($books, $book_count) = $this->booksServices->getBooks(
            genre: $genre,
            released_status: $released_status,
            limit: $limit,
            offset: $offset,
            genre_detail: $genre_detail,
        );

        foreach ($books as $index => $book) {
            // 日付フォーマット
            $books[$index]['release_date'] = \Common::formatSalesDate($book['release_date']);
            $books[$index]['release_date_int'] = $book['release_date'];
        }

        return response()->json([
            'books'         => $books,
            'book_count'    => $book_count
        ], 200);


    }


}
