from playwright.sync_api import sync_playwright
from rich import print
import sys

import time

with sync_playwright() as p:
    
    browser = p.chromium.launch()
    page = browser.new_page()
    
    url = f"https://books.rakuten.co.jp"
    title = f"【楽天ブックス限定特典+特典+他】ライザのアトリエ3 〜終わりの錬金術士と秘密の鍵〜 PS5版(アクリルキーホルダー+【早期購入封入特典】「サマーコーディネートコスチュームセット」DLC+他)"
    
    page.goto(url, timeout=0)

    # 入力欄に入力
    page.fill("#searchWords", title)
    time.sleep(1)
    
    # 検索ボタンクリック
    page.click("#searchBtn")
    time.sleep(1)

    # ヒットした一番上のゲームタイトルをクリック
    page.click("#ratArea > div > div:nth-child(1) > div.rbcomp__item-list__item__details > div.rbcomp__item-list__item__details__lead > h3 > a")
    print(page.title())
    time.sleep(1)
    
    image_url_list = page.locator('#imageSliderWrap > div.lSSlideOuter > ul > li')
    time.sleep(10)
    
    print(image_url_list)
    print(image_url_list.count())
    
    if image_url_list.count == 0:
        browser.close()
        print("画像が取得できませんでした")
        sys.exit()
    
    # 画像urlを取得
    for n in range(1, image_url_list.count()+1):
        print(n)
        
        # n番目の要素取得
        image_selector = image_url_list.nth(n)
        image_url = page.locator(f"#imageSliderWrap > div.lSSlideOuter > ul > li:nth-child({n}) > a > img").get_attribute('src')
        
        image_url = f"https:{image_url}"
        
        print(image_url)

    browser.close()
    
    