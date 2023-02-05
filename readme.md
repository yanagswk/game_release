# docker-compose実行
- ローカル環境  
docker-compose -f docker-compose-local.yaml up -d
- 本番環境  
docker-compose -f docker-compose-prod.yaml up -d


# 楽天api
- 楽天apiでゲームを取得するコマンド  
`php artisan command:rakutenGame "PS4" 1`  
`php artisan command:rakutenGame "PS5" 1`  
`php artisan command:rakutenGame "Switch" 1`  

- 楽天apiでゲームを取得するコマンド 
  - コミック  
  `php artisan command:rakutenBook 9 1`  88までやった
  - 文庫  
  `php artisan command:rakutenBook 2 1`  90までやった
  - 単行本  
  `php artisan command:rakutenBook 1 1`  90までやった
  - 図鑑  
  `php artisan command:rakutenBook 6 1`  
  - 絵本  
  `php artisan command:rakutenBook 7 1`  





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
