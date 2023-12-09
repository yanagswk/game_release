<?php

namespace App\Console\Commands;

use App\Console\Commands\Abstract\RakutenApi;
use App\Models\BooksItem;
use App\Models\CdDvdItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


/**
 * 楽天ブックスcd検索api叩く
 */
class RakutenBooksCd extends Command
{
    use RakutenApi;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rakutenCd {page}';

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
        // ページ数
        $page = $this->argument('page');

        $data_items = $this->requestRakutenApi(page:$page);

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
     * @return array
     */
    public function requestRakutenApi(int $page, int $size=null)
    {
        $client = new \GuzzleHttp\Client();

        // CD情報取得
        $response = $client->request(
            'GET',
            'https://app.rakuten.co.jp/services/api/BooksCD/Search/20170404',
            ['query' => [
                'format'        => 'json',
                'applicationId' => config('app.rakuten_app'),
                'affiliateId'   => config('app.rakuten_affi'),
                'booksGenreId'  => '002',
                'sort'          => '-releaseDate',
                'hits'          => '30',
                'page'          => $page,
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
                'title_kana'        => $item['Item']['titleKana'],
                'artist_name'       => $item['Item']['artistName'],
                'artist_name_kana'  => $item['Item']['artistNameKana'],
                'type'              => CdDvdItem::CD_ID,
                'label'             => $item['Item']['label'],
                'play_list'         => $item['Item']['playList'],
                'price'             => $item['Item']['itemPrice'],
                'sales_date'        => $this->formatSalesDate($item['Item']['salesDate']),
                'large_image_url'   => $item['Item']['largeImageUrl'],
                'item_url'          => $item['Item']['itemUrl'],
                'affiliate_url'     => $item['Item']['affiliateUrl'],
                'review_count'      => $item['Item']['reviewCount'],
                'review_average'    => $item['Item']['reviewAverage'],
                'item_caption'      => $item['Item']['itemCaption'],
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
        CdDvdItem::upsert(
            $contents,
            ['title'] // ユニークなカラム
        );
    }
}
