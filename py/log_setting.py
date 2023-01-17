import logging

def getMyLogger(name):
    logger = logging.getLogger(name)
    logger.setLevel(logging.INFO)
    if not logger.hasHandlers():    # ログの重複対策
        formatter = logging.Formatter('%(asctime)s - %(levelname)s:%(filename)s - %(message)s')
        # ファイルハンドラでtest.logにログを出力するように設定
        file_handler = logging.FileHandler('./log/rakuten_games.log')
        file_handler.setLevel(logging.INFO)
        file_handler.setFormatter(formatter)
        logger.addHandler(file_handler)
    return logger