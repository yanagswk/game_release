from playwright.sync_api import Playwright, sync_playwright, Page
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
from lib.chrome import get_my_chrome

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


def format_release_date(release_date_str) -> str:
    """
    Returns:
        日付フォーマット
    """
    result = re.sub(r"\D", "", release_date_str)    # 数字のみへ
    return result.ljust(8, '0')                     # 8桁になるように0埋め


def run(playwright: Playwright):
    """
        playwriteでゲーム画像のurlを取得する

        Parameters
        ----------
        games : list
            画像取得対象のゲーム一覧
    """
    
    # logger = getMyLogger(__name__)

    with get_my_chrome(playwright) as chrome:        

        with chrome.new_page() as page:
            
            # 001017005 本(ライトノベル/少年)
            # url = "https://books.rakuten.co.jp/calendar/001017005/weekly/?tid=2023-02-12&v=2&s=14"
            # url = "https://books.rakuten.co.jp/calendar/001017005/monthly/?tid=2023-02-14&v=2&s=14#rclist"
            url = "https://books.rakuten.co.jp/calendar/001017005/monthly/?tid=2023-02-01&v=2&s=14"
            # 楽天へ遷移
            page.goto(url, timeout=0)
            time.sleep(5)

            # next = page.query_selector('#main-container > div.rbcomp__pager-contents > div > div:nth-child(3) > a')
            next = page.locator('#main-container > div.rbcomp__pager-contents > div > div:nth-child(3) > a')
            is_next = True
            
            while is_next:
                print("----------------------------------------ページ----------------------------------------")

                # 本のセレクター
                book_list = page.locator('.item__panel')
                print(book_list.count())    # ページ内の本の数
                if book_list.count() == 0:
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
                    time.sleep(5)
                    print(page.title())

                    # 本のタイトル
                    title_selector = page.locator("#productTitle > h1")
                    book_info["title"] = title_selector.inner_text() if title_selector.is_visible() else ""
                    
                    # ジャンル(ライトノベル、コミックなど)
                    genre_selector = page.locator("#topicPath > dd > a:nth-child(3)")
                    book_info["genre"] = genre_selector.inner_text() if genre_selector.is_visible() else ""
                    
                    # ジャンル詳細(ジャンルに対しての詳細 少年、少女など)
                    genre_detail_selector = page.locator("#topicPath > dd > a:nth-child(4)")
                    book_info["genre_detail"] = genre_detail_selector.inner_text() if genre_detail_selector.is_visible() else ""
                    
                    # レーベル(GA文庫、電撃文庫、少年サンデーなど)
                    label_selector = page.locator("#productDetailedDescription > div > ul > li:nth-child(4) > span.categoryValue > a")
                    book_info["label"] = label_selector.inner_text() if label_selector.is_visible() else ""

                    # 本の作者
                    author_selector = page.locator("#productDetailedDescription > div > ul > li:nth-child(2) > span.categoryValue")
                    book_info["author"] = author_selector.inner_text() if author_selector.is_visible() else ""

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
                    book_info["page"] = page_selector.inner_text() if page_selector.is_visible() else ""
                    
                    # 発行形態
                    size_selector = page.locator(".productInfo:has-text(\"発行形態\") .categoryValue")
                    book_info["size"] = size_selector.inner_text() if size_selector.is_visible() else ""
                    
                    # 商品説明
                    description_selector = page.locator("#editArea2 > div > p:nth-child(2)")
                    book_info["description"] = description_selector.inner_text() if description_selector.is_visible() else ""

                    # 本の発売日
                    release_date_selector = page.locator(".productInfo:has-text(\"発売日\") .categoryValue")
                    book_info["release_date"] = format_release_date(release_date_selector.inner_text()) if release_date_selector.is_visible() else ""
                    
                    # 本の出版社
                    publisher_selector = page.locator(".productInfo:has-text(\"出版社\") .categoryValue")
                    book_info["publisher"] = publisher_selector.inner_text() if publisher_selector.is_visible() else ""
                    
                    # 本のISBN
                    isbn_selector = page.locator(".productInfo:has-text(\"ISBN\") .categoryValue")
                    book_info["isbn"] = isbn_selector.inner_text() if isbn_selector.is_visible() else ""
                    
                    # アフィリエイトリンク
                    # urlから数字のみを取得し、正常なら長さ配列の長さが1になる
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
                    time.sleep(5)

                # ページごとにインサート
                releasedModel = ReleasedModel()
                releasedModel.insert_books(books_data)
                
                # if next is None:
                #     break
                # with page.expect_navigation():
                #     # 「次の30件」へ
                #     next.click()
                
                # 「次の30件」が活性化状態ならTrue
                is_next = True if next.is_visible() else False
                if is_next:
                    # 「次の30件」へ遷移
                    page.goto(next.get_attribute('href'))
                    time.sleep(5)
        
        # browser.close()
        # return False


# if __name__ == '__main__':
    # books = get_not_game_image(0)
    # print(books)
    # get_books()
    
with sync_playwright() as p:
    run(p)
