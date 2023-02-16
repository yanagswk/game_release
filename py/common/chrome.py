from playwright.sync_api import Playwright, Browser
from contextlib import contextmanager

class MyChromeContextManager:

    user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.5112.20 Safari/537.36'

    def __init__(self, playwright: Playwright, launch_option: dict={}):
        self._playwright: Playwright = playwright
        self._launch_option = launch_option

    def __enter__(self):
        self._browser: Browser = self._playwright.chromium.launch(**self._launch_option)
        return self
        
    def __exit__(self, exc_type, value, traceback):
        if exc_type is None:
            self._browser.close()
        else:
            print(exc_type, value, traceback)
            return False

    @contextmanager
    def new_page(self, context_option: dict={}):
        context_option['user_agent'] = context_option.get('user_agent') or self.user_agent
        new_context = self._browser.new_context(**context_option)
        new_page = new_context.new_page()
        try:
            yield new_page
        finally:
            new_page.close()
            new_context.close()

def get_my_chrome(playwright: Playwright, launch_option: dict={}) -> MyChromeContextManager:
    launch_option['headless'] = launch_option['headless'] if 'headless' in launch_option else True
    return MyChromeContextManager(playwright=playwright, launch_option=launch_option)
