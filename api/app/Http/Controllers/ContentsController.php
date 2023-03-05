<?php

namespace App\Http\Controllers;

use App\Services\BooksServices;
use App\Services\CdsServices;
use App\Services\DvdBluRayServices;
use App\Services\GamesServices;
use Illuminate\Http\Request;

class ContentsController extends Controller
{

    public const BOOK_ID = 1;
    public const GAME_ID = 2;
    public const CD_ID = 3;
    public const DVD_ID = 4;

    public $gamesServices;
    public $booksServices;
    public $cdsServices;
    public $dvdBluRayServices;

    public function __construct()
    {
        $this->gamesServices = new GamesServices();
        $this->booksServices = new BooksServices();
        $this->cdsServices = new CdsServices();
        $this->dvdBluRayServices = new DvdBluRayServices();
    }

    /**
     * 検索結果を返す
     *
     * @param Request $request
     * @return void
     */
    public function getSearchContents (Request $request)
    {
        $search_word = $request->input('search_word');
        $genre = $request->input('genre');
        $genre_detail = $request->input('genre_detail');
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $user_id = $request->input('user_id');
        $category_id = $request->input('category_id');


        $contents =[];
        $contents_count = 0;

        if ((int)$category_id === self::BOOK_ID) {
            // 本を検索
            list($contents, $contents_count) = $this->booksServices->getBooks(
                limit: $limit,
                offset: $offset,
                genre: $genre,
                search_word: $search_word,
                genre_detail: $genre_detail,
            );
        } elseif ((int)$category_id === self::GAME_ID) {
            // ゲームを検索
            list($contents, $contents_count) = $this->gamesServices->getGames(
                limit: $limit,
                offset: $offset,
                genre: $genre,
                search_word: $search_word,
                genre_detail: $genre_detail,
            );
        } elseif ((int)$category_id === self::CD_ID) {
            // CDを検索
            list($contents, $contents_count) = $this->cdsServices->getCds(
                limit: $limit,
                offset: $offset,
                genre: $genre,
                search_word: $search_word,
                genre_detail: $genre_detail,
            );
        } elseif ((int)$category_id === self::DVD_ID) {
            logger('dvd');
            logger($request->all());
            // DVDを検索
            list($contents, $contents_count) = $this->dvdBluRayServices->getDvdBluRays(
                limit: $limit,
                offset: $offset,
                genre: $genre,
                search_word: $search_word,
                genre_detail: $genre_detail,
            );
        }

        return response()->json([
            'contents'          => $contents,
            'contents_count'    => $contents_count
        ], 200);


    }
}
