from playwright.sync_api import Playwright, sync_playwright, Page
from playwright.sync_api import sync_playwright
from rich import print
import sys
import time
import re
import os
import argparse
from pathlib import Path
from dotenv import load_dotenv

sys.path.append(str(Path(__file__).resolve().parent.parent))
from released_db import ReleasedModel
from log_setting import getMyLogger
from lib.chrome import get_my_chrome
from lib.common import Common

load_dotenv()

# def get_not_game_image(offset: int) -> list:
#     """
#         ゲーム画像のurlがないゲームを取得する

#         Parameters
#         ----------
#         offset : int
#             いくつ取得するか
#     """

#     releasedModel = ReleasedModel()
#     return releasedModel.get_books_item(offset)


def run(playwright: Playwright, count: int):
    """
        playwriteでゲーム情報を取得

        Parameters
        ----------
        games : list
            画像取得対象のゲーム一覧
    """
    
    # logger = getMyLogger(__name__)
    
    with get_my_chrome(playwright) as chrome:
        
        with chrome.new_page() as page:

            
            # Nintendo Switch
            url = "https://books.rakuten.co.jp/calendar/006514/monthly/?tid=2023-02-14&v=2&s=14"
            # # PS5
            # url = "https://books.rakuten.co.jp/calendar/006515/monthly/?tid=2023-02-14&v=2&s=14"
            # # PS4
            # url = "https://books.rakuten.co.jp/calendar/006513/monthly/?tid=2023-02-14&v=2&s=14"
            
            
            # 楽天へ遷移
            page.goto(url, timeout=0)
            time.sleep(5)
            
            # 次の30件を押し続ける
            # next = page.locator('#main-container > div.rbcomp__pager-contents > div > div:nth-child(3) > a')
            # is_next = True
            
            # while is_next:
            print(f"----------------------------------------{count}ページ----------------------------------------")

            # cdのセレクター
            cd_list = page.locator('.item__panel')
            print(cd_list.count())    # ページ内の本の数
            if cd_list.count() == 0:
                print("ゲームが存在しません")
                sys.exit()
            
            # 本の情報
            games_data = []
            for n in range(cd_list.count()):
            # for n in range(1):
                games_info = {}
                
                book = cd_list.nth(n)
                # 詳細ページのurl取得
                detail_page = book.locator(".item-image > a").get_attribute('href')
                # 詳細ページのurl
                games_info["page_url"] = detail_page
                
                # 詳細ページへ移動
                page.goto(detail_page, timeout=0)
                time.sleep(5)
                print(page.title())
                
                # cdのタイトル
                title_selector = page.locator("#productTitle > h1")
                games_info["title"] = Common.get_title(title_selector.inner_text()) if title_selector.is_visible() else ""
                
                # ジャンル
                genre_selector = page.locator("#topicPath > dd > a:nth-child(3)")
                games_info["genre"] = genre_selector.inner_text() if genre_selector.is_visible() else ""
                
                # ジャンル詳細
                genre_detail_selector = page.locator("#topicPath > dd > a:nth-child(4)")
                games_info["genre_detail"] = genre_detail_selector.inner_text() if genre_detail_selector.is_visible() else ""

                # 本の発売日
                release_date_selector = page.locator(".productInfo:has-text(\"発売日\") .categoryValue")
                games_info["release_date"]  = Common.format_release_date(release_date_selector.inner_text()) if release_date_selector.is_visible() else ""

                # cdの関連作品
                relation_item_selector = page.locator(".productInfo:has-text(\"関連作品\") .categoryValue")
                games_info["relation_item"] = relation_item_selector.inner_text() if relation_item_selector.is_visible() else ""

                # cdの発売元
                # selling_agency_selector = page.locator(".productInfo:has-text(\"発売元\") .categoryValue")
                # games_info["selling_agency"] = selling_agency_selector.inner_text() if selling_agency_selector.is_visible() else ""

                # cdの販売元
                distributor_selector = page.locator(".productInfo:has-text(\"販売元\") .categoryValue")
                games_info["distributor"] = distributor_selector.inner_text() if distributor_selector.is_visible() else ""

                # cdの品番
                game_number_selector = page.locator(".productInfo:has-text(\"品番\") .categoryValue")
                games_info["game_number"]  = game_number_selector.inner_text() if game_number_selector.is_visible() else ""
                
                # cdのJAN
                jan_selector = page.locator(".productInfo:has-text(\"JAN\") .categoryValue")
                games_info["jan"]  = jan_selector.inner_text() if jan_selector.is_visible() else ""
                
                # CERO区分
                cero_selector = page.locator(".productInfo:has-text(\"CERO区分\") .categoryValue")
                games_info["cero"]  = cero_selector.inner_text() if cero_selector.is_visible() else ""

                # 画像url
                # 画像が1つだけの場合と、複数ある場合でセレクターが異なる
                image_url_selector = page.locator("#oneImageWrap > a > img")
                if image_url_selector.is_visible():
                    image_url = image_url_selector.get_attribute('src')
                else:
                    image_url = page.locator("#imageSliderWrap > div.lSSlideOuter > ul > li:nth-child(1) > a > img").get_attribute('src')
                # gifの場合はスキップする
                games_info["image_url"] = f"https:{image_url}" if image_url not in 'gif' else ""

                # 商品説明
                description_selector = page.locator("#editArea2")
                games_info["description"]  = description_selector.inner_text() if description_selector.is_visible() else ""

                # アフィリエイトリンク
                # urlから数字のみを取得し、正常なら長さ配列の長さが1になる
                number_list = re.findall(r"\d+", detail_page)
                if not len(number_list) == 1:
                    print("urlから商品idが正常に取得できません")
                    print(detail_page)
                    continue
                games_info["rakuten_affiliate_url"] = f"https://hb.afl.rakuten.co.jp/ichiba/2cd5b0ae.6303a4de.2cd5b0af.dbaafed3/?pc=https%3A%2F%2Fitem.rakuten.co.jp%2Fbook%2F{number_list[0]}%2F&link_type=hybrid_url&ut=eyJwYWdlIjoiaXRlbSIsInR5cGUiOiJoeWJyaWRfdXJsIiwic2l6ZSI6IjI0MHgyNDAiLCJuYW0iOjEsIm5hbXAiOiJyaWdodCIsImNvbSI6MSwiY29tcCI6ImRvd24iLCJwcmljZSI6MCwiYm9yIjoxLCJjb2wiOjEsImJidG4iOjEsInByb2QiOjAsImFtcCI6ZmFsc2V9"
                
                print(games_info)
                games_data.append(games_info)

                # 一覧ページへ戻る
                page.go_back(timeout=0)
                time.sleep(5)
                

            # ページごとにインサート
            releasedModel = ReleasedModel()
            releasedModel.insert_games(games_data)

                # # # 「次の30件」が活性化状態ならTrue
                # is_next = True if next.is_visible() else False
                # if is_next:
                #     # 「次の30件」へ遷移
                #     page.goto(next.get_attribute('href'))
                #     time.sleep(5)
            


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument("page", help="ページ")
    args = parser.parse_args()
    print("page=" + args.page)

    with sync_playwright() as p:
        run(p, args.page)
