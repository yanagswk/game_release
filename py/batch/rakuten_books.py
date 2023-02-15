from playwright.sync_api import sync_playwright
from rich import print
import sys
import time
import re
import os
from pathlib import Path
from dotenv import load_dotenv

sys.path.append(str(Path(__file__).resolve().parent.parent))
from released_db import ReleasedModel
from log_setting import getMyLogger

load_dotenv()

def get_not_game_image(offset: int) -> list:
    """
        ゲーム画像のurlがないゲームを取得する

        Parameters
        ----------
        offset : int
            いくつ取得するか
    """

    releasedModel = ReleasedModel()
    return releasedModel.get_books_item(offset)


def get_books():
    """
        playwriteでゲーム画像のurlを取得する

        Parameters
        ----------
        games : list
            画像取得対象のゲーム一覧
    """
    
    # logger = getMyLogger(__name__)

    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()
        
        # releasedModel = ReleasedModel()
        
        # 001017005 本(ライトノベル/少年)
        # url = "https://books.rakuten.co.jp/calendar/001017005/weekly/?tid=2023-02-12&v=2&s=14"
        url = "https://books.rakuten.co.jp/calendar/001017005/monthly/?tid=2023-02-14&v=2&s=14#rclist"
        # 楽天へ遷移
        page.goto(url, timeout=0)
        time.sleep(1)
        
        # 次の30件を押し続ける
        next_page_selector = page.locator('#main-container > div.rbcomp__pager-contents > div > div:nth-child(3) > a')
        
        is_next_page = True
        # 「次の30件」が非活性化状態になるまで
        while is_next_page:
            print("----------------------------------------ページ----------------------------------------")
            print(page.title())
            time.sleep(1)

            # 本のセレクター
            book_list = page.locator('.item__panel')
            print(book_list.count())    # ページ内の本の数
            if book_list.count() == 0:
                browser.close()
                print("本が存在しません")
                sys.exit()
            
            # 本の情報
            books_data = []
            for n in range(book_list.count()):
                book_info = {}
                
                book = book_list.nth(n)
                # 詳細ページのurl取得
                detail_page = book.locator(".item-image > a").get_attribute('href')
                # 詳細ページのurl
                book_info["page_url"] = detail_page
                
                # 詳細ページへ移動
                page.goto(detail_page, timeout=0)
                time.sleep(1)
                print(page.title())
                time.sleep(1)
                
                # 本のタイトル
                book_info["title"] = page.locator("#productTitle > h1").inner_text()
                
                # ジャンル(ライトノベル、コミックなど)
                book_info["genre"] = page.locator("#topicPath > dd > a:nth-child(3)").inner_text()
                
                # ジャンル詳細(ジャンルに対しての詳細 少年、少女など)
                book_info["junru_details"] = page.locator("#topicPath > dd > a:nth-child(4)").inner_text()
                
                # レーベル(GA文庫、電撃文庫、少年サンデーなど)
                book_info["label"] = page.locator("#productDetailedDescription > div > ul > li:nth-child(4) > span.categoryValue > a").inner_text()

                # 本の作者
                book_info["author"] = page.locator("#productDetailedDescription > div > ul > li:nth-child(2) > span.categoryValue").inner_text()

                # 画像url
                # 画像が1つだけの場合と、複数ある場合でセレクターが異なる
                image_url_selector = page.locator("#oneImageWrap > a > img")
                if image_url_selector.is_visible():
                    image_url = image_url_selector.get_attribute('src')
                else:
                    image_url = page.locator("#imageSliderWrap > div.lSSlideOuter > ul > li:nth-child(1) > a > img").get_attribute('src')
                # gifの場合はスキップする
                book_info["image_url"] = f"https:{image_url}" if image_url not in 'gif' else ""
                
                # 本のシリーズ
                series_selector = page.locator(".productInfo:has-text(\"シリーズ\") .categoryValue")
                book_info["series"] = series_selector.inner_text() if series_selector.is_visible() else ""
                
                # ページ数
                page_selector = page.locator(".productInfo:has-text(\"ページ数\") .categoryValue")
                book_info["series"] = page_selector.inner_text() if page_selector.is_visible() else ""
                
                # 発行形態
                book_info["size"] = page.locator(".productInfo:has-text(\"発行形態\") .categoryValue").inner_text()
                
                # 商品説明
                book_info["description"] = page.locator("#editArea2 > div > p:nth-child(2)").inner_text()
                
                # 本の発売日
                book_info["release_date"] = page.locator(".productInfo:has-text(\"発売日\") .categoryValue").inner_text()
                
                # 本の出版社
                book_info["publisher"] = page.locator(".productInfo:has-text(\"出版社\") .categoryValue").inner_text()
                
                # 本のISBN
                book_info["isbn"] = page.locator(".productInfo:has-text(\"ISBN\") .categoryValue").inner_text()
                
                # アフィリエイトリンク
                # urlから数字のみを取得し、正常なら長さが1つの配列になる
                number_list = re.findall(r"\d+", detail_page)
                if not len(number_list) == 1:
                     print("urlから商品idが正常に取得できません")
                     print(detail_page)
                     continue
                book_info["rakuten_affiliate_url"] = f"https://hb.afl.rakuten.co.jp/ichiba/2cd5b0ae.6303a4de.2cd5b0af.dbaafed3/?pc=https%3A%2F%2Fitem.rakuten.co.jp%2Fbook%2F{number_list[0]}%2F&link_type=hybrid_url&ut=eyJwYWdlIjoiaXRlbSIsInR5cGUiOiJoeWJyaWRfdXJsIiwic2l6ZSI6IjI0MHgyNDAiLCJuYW0iOjEsIm5hbXAiOiJyaWdodCIsImNvbSI6MSwiY29tcCI6ImRvd24iLCJwcmljZSI6MCwiYm9yIjoxLCJjb2wiOjEsImJidG4iOjEsInByb2QiOjAsImFtcCI6ZmFsc2V9"
                
                print(book_info)
                books_data.append(book_info)
                
                # 一覧ページへ戻る
                page.go_back(timeout=0)
                time.sleep(1)
            
            # 「次の30件」が活性化状態ならTrue
            is_next_page = True if next_page_selector.is_visible() else False
            if is_next_page:
                # 「次の30件」へ遷移
                page.goto(next_page_selector.get_attribute('href'))
        
        browser.close()
        return False
        
        

        # 写真データをインサート
        # releasedModel.insert_game_image(games[index]["id"], image_list, genre)


if __name__ == '__main__':
    # books = get_not_game_image(0)
    # print(books)
    get_books()
