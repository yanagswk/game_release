# docker-compose実行
- ローカル環境  
docker-compose -f docker-compose-local.yaml up -d
- 本番環境  
docker-compose -f docker-compose-prod.yaml up -d


# 楽天api
- ゲーム取得  
`php artisan command:rakutenGame "PS4" 1`  
`php artisan command:rakutenGame "PS5" 1`  
`php artisan command:rakutenGame "Switch" 1`  

- 本取得 
  - コミック  
  `php artisan command:rakutenBook 9 1`  80までやった
  男運ゼロの薬師令嬢、初恋の黒騎士様が押しかけ婚約者になりまして。　3巻 : 1/31発売  2/10 7:00の段階で「90」ページにある  
  →「95」ページになった
  更新頻度: 高
  - 文庫  
  `php artisan command:rakutenBook 2 1`  90までやった 
  5日経って1冊しか更新されていない
  更新頻度: 低  
  3日に1回数？

  - 単行本  
  `php artisan command:rakutenBook 1 1`  80までやった
  今に向き合い、次につなぐ : 3/7発売  2/10 7:00の段階で「90」ページにある  
  更新頻度: 高  

  - 図鑑  
  `php artisan command:rakutenBook 6 1`  1から10まで  
  更新頻度: 低
  - 絵本  
  `php artisan command:rakutenDb 7 1`    1から10まで  
  更新頻度: 低

- CD取得  
  `php artisan command:rakutenCd 1`       81までやった
  【輸入盤】Jaroslav Tuma: Johann David Sieber Organ : 1/31発売  2/10 7:00の段階で「90」ページにある  
  更新頻度: 高

- DVD/Blu-ray取得  
`php artisan command:rakutenDb 1`       80までやった
  クビキリサイクル 青色サヴァンと戯言遣い Blu-ray Disc BOX【完全生産限定版】【Blu-ray】 : 2/22発売  2/10 7:00の段階で「90」ページにある  
  更新頻度: 高


# コード補完
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

