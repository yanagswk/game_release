<?php

namespace App\Console\Commands\Series;

use App\Services\BooksServices;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegisterSeriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:RegisterSeries';

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

        $booksServices = new BooksServices();

        // シリーズを登録していないコンテンツを取得
        $books_info = $booksServices->getUnregisteredSeriesContents();

        // idのみ
        $id_books = array_column($books_info, 'id');
        // id除外
        $books = array_map(function($book) {
            return [
                'series' => $book['series'],
                'author' => $book['author'],
            ];
        }, $books_info);

        // 更新処理
        try {
            DB::transaction(function () use($booksServices, $books, $id_books) {
                // シリーズマスターデータへインサート
                $booksServices->insertSeriesTitles($books);
                // シリーズチェックフラグ更新
                $booksServices->updateSeriesChecked($id_books);
            });
            echo("更新完了\n");

        } catch(\Exception $e) {
            logger($e);
            echo("更新失敗\n");
        }

        return true;
    }
}
