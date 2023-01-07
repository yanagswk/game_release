from playwright.sync_api import sync_playwright
from rich import print
import sys
import time

from released_db import ReleasedModel
from log_setting import getMyLogger

def get_not_game_image(offset: int) -> list:
    """
        ゲーム画像のurlがないゲームを取得する

        Parameters
        ----------
        offset : int
            いくつ取得するか
    """

    releasedModel = ReleasedModel()
    return releasedModel.get_not_exit_game_image(offset)


def get_game_image(games: list[str]):
    """
        playwriteでゲーム画像のurlを取得する

        Parameters
        ----------
        games : list
            画像取得対象のゲーム一覧
    """
    
    logger = getMyLogger(__name__)

    with sync_playwright() as p:

        for index in range(len(games)):
            logger.info("------------------------取得スタート------------------------")
            logger.info(f"{games[index]['id']}: {games[index]['title']}")
            print(f"{games[index]['id']}: {games[index]['title']}")
        
            browser = p.chromium.launch()
            page = browser.new_page()
            
            url = "https://books.rakuten.co.jp"

            # 楽天へ遷移
            page.goto(url, timeout=0)

            time.sleep(10)

            title = str_replace(games[index]["title"])

            # 入力欄に入力
            page.fill("#searchWords", title)
            time.sleep(1)

            # 検索ボタンクリック
            page.click("#searchBtn")
            time.sleep(1)

            # ヒットした一番上のゲームタイトルをクリック
            game_title_selector = "#ratArea > div > div:nth-child(1) > div.rbcomp__item-list__item__details > div.rbcomp__item-list__item__details__lead > h3 > a"
            game_title = page.locator(game_title_selector)
            if not game_title.is_visible():
                logger.error(f"{games[index]['id']}: 検索に引っかかりませんでした")
                browser.close()
                continue

            page.click(game_title_selector)
            time.sleep(1)
            
            # 画像一覧
            image_url_list = page.locator('#imageSliderWrap > div.lSSlideOuter > ul > li')
            time.sleep(10)

            if image_url_list.count == 0:
                browser.close()
                logger.error(f"{games[index]['id']}: 画像が取得できませんでした")
                continue
            
            image_list = []
            # 画像urlを取得
            for n in range(1, image_url_list.count()+1):
                # n番目の要素取得
                image_url = page.locator(f"#imageSliderWrap > div.lSSlideOuter > ul > li:nth-child({n}) > a > img").get_attribute('src')
                image_url = f"https:{image_url}"
                image_list.append(image_url)
            logger.info(f"画像枚数: {image_url_list.count()}枚")
                
            # ジャンル
            genre = page.locator('#topicPath > dd > a:nth-child(4)')
            genre = genre.inner_text() if genre.is_visible() else ""
            logger.info(f"ジャンル: {genre}")

            browser.close()
            
            # 写真データをインサート
            releasedModel = ReleasedModel()
            releasedModel.insert_game_image(games[index]["id"], image_list, genre)

def str_replace(target: str) -> str:
    """
        特殊文字を変換
    """
    target = target.replace('\u3000', '　')
    return target

if __name__ == '__main__':
    games = get_not_game_image(4)
    print(games)
    get_game_image(games)
