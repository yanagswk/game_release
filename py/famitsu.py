from playwright.sync_api import sync_playwright
from rich import print
import argparse
import sys
import time
from datetime import datetime as dt

from released_db import ReleasedModel


def get_famitsu(tagert_date: int):
    """
        ファミ通から記事をスクレイピングする

        Parameters
        ----------
        day : int
            日付 例(20221229)
    """
    
    if not len(tagert_date) == 8:
        print("年月日の8桁で指定してください")
        sys.exit()
    
    year = tagert_date[0:4]
    month = tagert_date[4:6]
    day = tagert_date[6:8]

    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()

        url = f"https://www.famitsu.com/back/?year={year}&month={month}&day={day}&order=desc"
        # ファミ通へ遷移
        page.goto(url, timeout=0)
        
        print(page.title())

        # 何ページあるか
        paging = page.locator('.ft-pager__item')
        if paging.count() == 0: # 1ページしかない場合
            paging = 1
        else:
            paging = paging.count() - 1 # 「次へ」ボタンの分を引く
        print(paging)
        
        # ページ数分
        for p in range(paging):
            url = f"https://www.famitsu.com/back/?year={year}&month={month}&day={day}&order=desc&page={p+1}"
            # ファミ通へ遷移
            page.goto(url, timeout=0)
            print(url)
            time.sleep(3)
            print(f"--------------------------{p+1}ページ目----------------------------")

            # 記事一覧のセレクター
            article_list = page.locator('.card__inner')

            if article_list.count() == 0:
                browser.close()
                print("記事が取得できませんでした")
                sys.exit()
            
            print(article_list.count())
            print(article_list)

            date = []
            for n in range(article_list.count()):
                article_obj = {}
                article = article_list.nth(n)
                
                # 広告判定 (スキップする)
                article_post = article.locator('.card__footer > .card__ad')
                if article_post.is_visible():
                    print("-------------------広告だからスキップしますん-------------------")                    
                    continue

                # 記事のタイトル
                article_title = article.locator('.card__title > a')
                article_title = article_title.inner_text() if article_title.is_visible() else ""
                print(article_title)
                article_obj['title'] = article_title
                
                # 記事のurl
                article_url_selector = article.locator('.card__title > a')
                article_url = article_url_selector.get_attribute('href') if article_url_selector.is_visible() else ""
                if "https" in article_url:
                    article_obj['url'] = article_url
                else:
                    article_obj['url'] = f"https://www.famitsu.com{article_url}"
                print(article_obj['url'])
                
                # 記事のトップ画像url
                article_img = article.locator('.media-image')
                article_img = article_img.get_attribute('data-src') if article_img.is_visible() else ""
                print(article_img)
                article_obj['img'] = article_img
                
                # 記事のジャンル
                article_genre = article.locator('.card__category-item')
                genre_list = []
                for j in range(article_genre.count()):
                    genru = article_genre.nth(j)
                    genre_list.append(genru.inner_text())
                article_obj['genre'] = ",".join(genre_list)
                
                # 投稿日時
                article_post = article.locator('.card__footer > .card__date > .card__date-time')
                article_post = article_post.inner_text() if article_post.is_visible() else ""
                article_obj['post_date'] = dt.strptime(article_post, '%Y-%m-%d %H:%M:%S') if article_post else dt.strptime(tagert_date, '%Y%m%d')
                
                date.append(article_obj)

            # print(date)
            releasedModel = ReleasedModel()
            releasedModel.insert_article(date, 2)
        
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
    
    game = ReleasedModel().exit_date_game_article(2, tagert_date_datatime)

    if game:
        print("記事を取得したことがあります")
        sys.exit()
    
    get_famitsu(args.tagert_date)
