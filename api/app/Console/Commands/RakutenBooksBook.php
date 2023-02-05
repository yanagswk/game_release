<?php

namespace App\Console\Commands;

use App\Console\Commands\Abstract\RakutenApi;
use App\Models\BooksItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


/**
 * 楽天ブックス書籍検索api叩く
 */
class RakutenBooksBook extends Command
{
    use RakutenApi;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rakutenBook {size} {page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * 楽天APIを叩く
     *
     * @return int
     */
    public function handle()
    {
        // ジャンル
        $size = $this->argument('size');
        // ページ数
        $page = $this->argument('page');

        $data_items = $this->requestRakutenApi(page:$page, size:$size);

        logger($data_items);

        $books_list = $this->formatRakutenApiBody($data_items);

        $this->addRakutenApiData($books_list);

        echo("取得完了\n");

        return true;
    }

    /**
     * 楽天apiを叩く
     *
     * @param int $page ページ数(100まで)
     * @param int $size 書式のタイプ(9:コミック, 2:文庫, 1:単行本, 6:図鑑, 7:絵本)
     * @return array
     */
    public function requestRakutenApi($page, $size)
    {
        $client = new \GuzzleHttp\Client();

        // 書籍情報取得
        $response = $client->request(
            'GET',
            'https://app.rakuten.co.jp/services/api/BooksBook/Search/20170404',
            ['query' => [
                'format'            => 'json',
                'applicationId'     => config('app.rakuten_app'),
                'affiliateId'       => config('app.rakuten_affi'),
                'booksGenreId'      => '001',   // ジャンル(本)
                'size'              => $size,   // 書式のタイプ(コミック)
                'sort'              => '-releaseDate',
                'hits'              => 30,
                'page'              => $page,
                'availability'      => 0,
                'outOfStockFlag'    => 1,
                'carrier'           => 0,
            ]]
        );
        $data = json_decode($response->getBody(), true);
        return $data['Items'];
    }

    /**
     * 楽天apiの情報を整形する
     * @param array $contents apiデータ
     * @return array
     */
    public function formatRakutenApiBody($contents)
    {
        $books_list = array_map(function($item) {
            return [
                'title'             => $item['Item']['title'],
                'size'              => $item['Item']['size'],
                'price'             => $item['Item']['itemPrice'],
                'sales_date'        => $this->formatSalesDate($item['Item']['salesDate']),
                'large_image_url'   => $item['Item']['largeImageUrl'],
                'item_url'          => $item['Item']['itemUrl'],
                'affiliate_url'     => $item['Item']['affiliateUrl'],
                'author'            => $item['Item']['author'],
                'publisherName'     => $item['Item']['publisherName'],
                'review_count'      => $item['Item']['reviewCount'],
                'review_average'    => $item['Item']['reviewAverage'],
                'item_caption'      => $item['Item']['itemCaption'],
                'type'              => $item['Item']['seriesName'],
                'contents'          => $item['Item']['contents'],
                'created_at'        => Carbon::now('Asia/Tokyo'),
                'updated_at'        => Carbon::now('Asia/Tokyo')
            ];
        }, $contents);
        return $books_list;
    }

    /**
     * 楽天apiのデータをdbに追加する
     * @param array $contents apiデータ
     * @return void
     */
    public function addRakutenApiData($contents)
    {
        // 存在しなければ、更新・新規作成
        BooksItem::upsert(
            $contents,
            ['title'] // ユニークなカラム
        );
    }
}
