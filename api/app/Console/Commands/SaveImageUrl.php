<?php

namespace App\Console\Commands;

use App\Models\GameImage;
use App\Models\Games;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\throwException;

class SaveImageUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:saveImageUrl';

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
        // メイン画像を設定しているリレーション先(ゲーム画像テーブル)が存在しない、ゲーム一覧を取得
        $games = Games::with('game_image')
            // 存在しないリレーション
            ->whereDoesntHave('game_image', function($query) {
                $query->where('image_type', GameImage::MAIN_IMG);
            })
            ->get()
            ->toArray();

        if (!count($games)) {
            echo "全てのゲームは画像設定済みです。";
            echo "\n";
            exit();
        }

        $main_img_name = config('app.main_img_name');
        $img_dir = config('app.img_dir');

        $game_list = array_map(function($game) use($main_img_name) {
            if (empty($game['large_image_url'])) {
                throw new \Exception("画像ないよ");
            }
            return [
                'game_id'       => $game['id'],
                'image_type'    => GameImage::MAIN_IMG,
                'img_url'       => "{$game['id']}/{$main_img_name}",
                'created_at'    => Carbon::now('Asia/Tokyo'),
                'updated_at'    => Carbon::now('Asia/Tokyo')
            ];
        }, $games);

        // 画像url追加
        GameImage::insert($game_list);

        // 画像パスがdbに保存できたので、画像をサーバーに保存
        foreach ($games as $game => $item) {
            $path = "{$img_dir}/{$item['id']}/{$main_img_name}";
            Storage::put(
                $path,
                file_get_contents($item['large_image_url'])
            );
        }
    }
}
