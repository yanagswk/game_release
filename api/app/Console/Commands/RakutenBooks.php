<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $client = new \GuzzleHttp\Client();

        // 楽天api実行
        $reqponse = $client->request(
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
                'page' => '1',
            ]]
            );

        $data = json_decode($reqponse->getBody(), true);

        // var_dump($data);

        \Log::debug($data);


        return 0;
    }
}
