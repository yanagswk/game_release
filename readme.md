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


firewall-cmd --list-all
firewall-cmd --reload

firewall-cmd --permanent --list-rich-rules
firewall-cmd --permanent --remove-rich-rule=''

firewall-cmd --permanent --zone=public --add-rich-rule="rule family="ipv4" source address="172.28.1.5" port protocol="tcp" port="3306" accept"
firewall-cmd --add-source=172.28.1.5/24 --zone=public --permanent
firewall-cmd --add-source=172.28.1.5/3306 --zone=public --permanent
firewall-cmd --remove-source=172.28.1.5/24 --zone=public --permanent

mysql -u root -h host.docker.internal -p