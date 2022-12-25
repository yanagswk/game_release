<?php

namespace App\Console\Commands;

use App\Models\Games;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\map;

// use GuzzleHttp\Client;


class RakutenBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rakuten {hardware} {page}';

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

        $hardware = $this->argument('hardware');
        $page = $this->argument('page');

        $client = new \GuzzleHttp\Client();

        // api実行
        $response = $client->request(
            'GET',
            'https://app.rakuten.co.jp/services/api/BooksGame/Search/20170404',
            ['query' => [
                'format'        => 'json',
                'applicationId' => '1092593678658310389',
                'affiliateId'   => '2c1ecae8.486864ad.2c1ecae9.2490d2f1',
                'booksGenreId'  => '006',
                'hardware'      => $hardware,
                'sort'          => '-releaseDate',
                'hits'          => '30',
                'page'          => $page,
            ]]
        );
        $data = json_decode($response->getBody(), true);
        $data_items = $data['Items'];

        $game_list = array_map(function($item) {

            // Nintendo Switchの場合、ハードウェア名変更
            $hardware = $item['Item']['hardware'] === 'Nintendo Switch' ? 'Switch' : $item['Item']['hardware'];

            // 数字のみにする
            $sales_date_number = preg_replace('/[^0-9]/', '', $item['Item']['salesDate']);

            // 6文字未満の場合
            // if (mb_strlen($sales_date_number) < 6) {
            //     throw new Exception("発売日エラー: {$item['Item']['salesDate']} {$item['Item']['itemPrice']}");
            // }

            // 8桁になるように0埋め
            $sales_date = str_pad($sales_date_number, 8, 0, STR_PAD_RIGHT);

            return [
                'title' => $item['Item']['title'],
                'hardware' => $hardware,
                'price' => $item['Item']['itemPrice'],
                'sales_date' => $sales_date,
                'large_image_url' => $item['Item']['largeImageUrl'],
                'item_url' => $item['Item']['itemUrl'],
                'affiliate_url' => $item['Item']['affiliateUrl'],
                'label' => $item['Item']['label'],
                'item_caption' => $item['Item']['itemCaption'],
                'review_count' => $item['Item']['reviewCount'],
                'review_average' => $item['Item']['reviewAverage'],
                'created_at' => Carbon::now('Asia/Tokyo'),
                'updated_at' => Carbon::now('Asia/Tokyo')
            ];
        }, $data_items);

        // 存在しなければ、更新・新規作成
        Games::upsert(
            $game_list,
            ['title', 'hardware'] // ユニークなカラム
        );

        echo("取得完了\n");

        return true;
    }
}
