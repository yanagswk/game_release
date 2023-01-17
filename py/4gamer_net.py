from playwright.sync_api import sync_playwright
from rich import print
import argparse
import sys
import time
from datetime import datetime as dt

from released_db import ReleasedModel


def get_4gamer_net(day: int):
    """
        4gamerから記事をスクレイピングする

        Parameters
        ----------
        day : int
            日付 例(20221229)
    """
    
    games = ReleasedModel().get_exit_game_article(1, day)
    game_title_list = [game["title"] for game in games]
    
    print(game_title_list)
    
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()
        
        # アクティブページはカウントされないから+1する
        
        url = f"https://www.4gamer.net/script/search/index.php?mode=article&DATE={day}"
        # 4gamerへ遷移
        page.goto(url, timeout=0)

        # 何ページあるか
        paging = page.locator('#SEARCH_result > ul.paging > li.pages > a')
        time.sleep(3)
        paging = paging.count()+1  
        print(paging)
        
        # ページ数分
        for p in range(paging):
            # p =+1
            url = f"https://www.4gamer.net/script/search/index.php?mode=article&DATE={day}&page={p+1}"
            # 4gamerへ遷移
            page.goto(url, timeout=0)
            
            time.sleep(3)
            
            print(f"--------------------------{p+1}ページ目----------------------------")

            # 記事のセレクター
            article_list = page.locator('.V2_article_container')

            if article_list.count == 0:
                browser.close()
                print("記事が取得できませんでした")
                sys.exit()
            
            print(article_list)
            
            date = []
            for n in range(article_list.count()):
                article_obj = {}
                article = article_list.nth(n)

                # 記事タイトル
                article_title = article.locator('h2')
                article_title = article_title.inner_text() if article_title.is_visible() else ""
                print(article_title)

                # すでに取得している記事ならスキップ
                if article_title in game_title_list:
                    print("取得している記事ですた")
                    continue

                article_obj['title'] = article_title
                
                # 記事のurl
                article_url_selector = article.locator('h2 > a')
                article_url = f"https://www.4gamer.net{article_url_selector.get_attribute('href')}" if article_url_selector.is_visible() else ""
                article_obj['url'] = article_url
                print(article_url)
                
                # 記事のトップ画像url
                article_img = article.locator('.img_right_top')
                article_img = f"https://www.4gamer.net{article_img.get_attribute('src')}" if article_img.is_visible() else ""
                print(article_img)
                article_obj['img'] = article_img
                
                # 投稿日時
                article_post = article.locator('.V2_time_container')
                article_post = article_post.inner_text() if article_post.is_visible() else ""
                print(article_post)
                if article_post:
                    article_post = article_post.replace('［', "").replace('］', "")
                    article_obj['post_date'] = dt.strptime(article_post, '%Y/%m/%d %H:%M')
                else:
                    article_obj['post_date'] = dt.strptime(day, '%Y%m%d')
                
                # ジャンル
                article_obj['genre'] = ""
                
                date.append(article_obj)

            releasedModel = ReleasedModel()
            releasedModel.insert_article(date, 1)
        
        browser.close()


if __name__ == '__main__':
    
    parser = argparse.ArgumentParser()
    parser.add_argument("tagert_date")
    args = parser.parse_args( )
    print(args.tagert_date)
    
    if not args.tagert_date.isdigit() or len(args.tagert_date) != 8:
        print("年月日は8桁で入力してください")
        sys.exit()
    tagert_date_datatime = dt.strptime(args.tagert_date, '%Y%m%d')
    
    # game = ReleasedModel().exit_date_game_article(1, tagert_date_datatime)

    # if game:
    #     print("記事を取得したことがあります")
    #     sys.exit()
    
    get_4gamer_net(args.tagert_date)
