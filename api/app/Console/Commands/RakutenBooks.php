<?php

namespace App\Console\Commands;

use App\Models\Games;
use Carbon\Carbon;
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
    protected $signature = 'command:rakuten';

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

        $client = new \GuzzleHttp\Client();

        // api実行
        $response = $client->request(
            'GET',
            'https://app.rakuten.co.jp/services/api/BooksGame/Search/20170404',
            ['query' => [
                'format' => 'json',
                'applicationId' => '1092593678658310389',
                'booksGenreId' => '006',
                // 'title' => '$title',
                'hardware' => 'PS5',
                'sort' => '-releaseDate',
                'hits' => '30',
                'page' => '6',
            ]]
        );
        $data = json_decode($response->getBody(), true);
        $data_items = $data['Items'];

        $game_list = array_map(function($item) {

            return [
                'title' => $item['Item']['title'],
                'hardware' => $item['Item']['hardware'],
                'price' => $item['Item']['itemPrice'],
                'sales_date' => $item['Item']['salesDate'],
                'large_image_url' => $item['Item']['largeImageUrl'],
                'item_url' => $item['Item']['itemUrl'],
                'label' => $item['Item']['label'],
                'item_caption' => $item['Item']['itemCaption'],
                'review_count' => $item['Item']['reviewCount'],
                'review_average' => $item['Item']['reviewAverage'],
                'created_at' => Carbon::now('Asia/Tokyo'),
                'updated_at' => Carbon::now('Asia/Tokyo')
            ];
        }, $data_items);

        // \Log::debug($game_list);

        // ゲームデータ追加
        Games::insert($game_list);

        echo("取得完了\n");

        return true;
    }
}
