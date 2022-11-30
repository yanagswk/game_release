import time

from playwright.sync_api import Playwright, sync_playwright, Page

from models.local_docker_base_model import LocalDockerBaseModel
from modules.my_chrome import get_my_chrome
from modules.common import print_log


def run(playwright: Playwright) -> int:
    
    model = LocalDockerBaseModel()

    with get_my_chrome(playwright) as chrome:

        with chrome.new_page() as page:
            url = f"https://books.rakuten.co.jp/search?g=006515&s=8&e=5&l-id=parts-best-seller-list"
            
            # 一覧画面へ遷移
            page.goto(url)
            print_log('page:' + page.url)
            
            while True:
                time.sleep(3)
                
                # 表示されている全ての各要素を取得
                page_list = page.locator('.rbcomp__item-list__item')
                
                for n in range(page_list.count()):
                    
                    # n番目の要素取得
                    main_layout = page_list.nth(n)
                    
                    time.sleep(3)
                    
                    with chrome.new_page() as page_syosai_layout:
                        # 詳細ページのリンク取得
                        syosai_url = main_layout.locator("h3 a").get_attribute('href')
                        # 詳細画面へ遷移
                        page_syosai_layout.goto(syosai_url)
                        print_log(f'syosai:{page_syosai_layout.url}')
                        
                        ######## 情報を取得する ########
                        # タイトル
                        title = page_syosai_layout.locator("#productTitle h1")
                        title = title.inner_text().split("\n")[0] if title.is_visible() else ""

                        # 発売日
                        release_date = page_syosai_layout.locator("span:has-text(\"発売日\") + .categoryValue")
                        release_date = release_date.inner_text() if release_date.is_visible() else ""
                            
                        # 画像URL
                        image_url = page_syosai_layout.locator("#imageSlider li.active img").get_attribute('src')
                        
                        # ハードウェア
                        hardware = page_syosai_layout.locator("span:has-text(\"対応機種等\") + .categoryValue") # +で次の要素になる
                        hardware = hardware.inner_text() if hardware.is_visible() else ""
                        
                        # 商品紹介
                        contents = page_syosai_layout.locator("#editArea2 h2:has-text(\"商品説明\") + .free")   # +で次の要素になる
                        contents = contents.inner_text() if contents.is_visible() else ""
                        
                        # データ保存
                        model.insert_game_info([
                            title,
                            hardware,
                            image_url,
                            release_date,
                            contents
                        ])
                            
                                
with sync_playwright() as playwright:
    run(playwright)