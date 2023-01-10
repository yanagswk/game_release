<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\GetArticleListRequest;
use App\Models\GameArticle;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 記事一覧取得
     *
     * @param Request $request
     * @return void
     */
    public function getArticle(GetArticleListRequest $request)
    {
        $site_id = $request->input('site_id');
        $post_type = $request->input('post_type');
        $post_date = $request->input('post_date');
        $offset = $request->input('offset');
        $limit = $request->input('limit');

        $game_article = GameArticle::query()
            ->with('site_master:id,site_name')
            ->orderBy('post_date', 'desc');

        if ($site_id) {
            $game_article->where('site_id', $site_id);
        }

        if ($post_type == "target") {
            $game_article->whereDate('post_date', $post_date);
        }

        $game_article_copy = clone $game_article;
        $article_count = count($game_article_copy->get());

        $game_article = $game_article->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();

        $article_list = array_map(function($article) {
            return [
                'id'                => $article["id"],
                'title'             => $article["title"],
                'site_url'          => $article["site_url"],
                'genre'             => $article["genre"],
                'top_image_url'     => $article["top_image_url"],
                'post_date'         => $article["post_date"],
                'site_id'           => $article["site_master"]["id"],
                'site_name'       => $article["site_master"]["site_name"]
            ];
        }, $game_article);

        return response()->json([
            'message'           => 'success',
            'article_count'     => $article_count,
            'game_article'      => $article_list,
        ], 200);
    }
}
