# api
- 楽天apiコマンド  
`php artisan command:rakuten "PS4" 1`  
`php artisan command:rakuten "PS5" 1`  
`php artisan command:rakuten "Switch" 1`  



<!-- php artisan command:rakuten "PS4" 10 -->

<!-- php artisan command:rakuten "PS5" 8 -->

<!-- php artisan command:rakuten "Switch" 15 -->

- コード補完するライブラリ  
barryvdh/laravel-ide-helper  
参考: https://chigusa-web.com/blog/laravel-ide-helper/   
自作ファザード作成時に、コード補完するために以下のコマンド実行  
`php artisan ide-helper:generate`


# スクレイピング
- ゲームの画像・ジャンル取得  
`python3 rakuten_games.py`
- ファミ通の記事取得  
`python3 famitsu.py xxxxxxxx` (引数は年月日8桁) 
- 4gamerの記事取得  
`python3 4gamer_net.py xxxxxxxx` (引数は年月日8桁) 
