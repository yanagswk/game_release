<?php

namespace App\Console\Commands;

use App\Models\Games;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
                'hardware' => 'PS4',
                'sort' => '-releaseDate',
                'hits' => '30',
                'page' => '5',
            ]]
        );
        $data = json_decode($response->getBody(), true);
        $data_items = $data['Items'];

        $game_list = array_map(function($item) {
            // 日付変換
            $sales_date = Carbon::createFromFormat(
                'Y年m月d日',
                $item['Item']['salesDate']
            );
            return [
                'title' => $item['Item']['title'],
                'hardware' => $item['Item']['hardware'],
                'price' => $item['Item']['itemPrice'],
                'sales_date' => $sales_date,
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

        // 追加
        Games::insert($game_list);

        return true;
    }
}
