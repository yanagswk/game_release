import logging
from pytz import timezone
from datetime import datetime
import sys
from pathlib import Path

sys.path.append(str(Path(__file__).resolve().parent.parent))

def getMyLogger(name):
    logger = logging.getLogger(name)
    logger.setLevel(logging.INFO)
    if not logger.hasHandlers():    # ログの重複対策
        formatter = logging.Formatter('%(asctime)s - %(levelname)s:%(filename)s - %(message)s')
        formatter.converter = customTime
        # ファイルハンドラでtest.logにログを出力するように設定
        file_handler = logging.FileHandler('log/rakuten_games.log')
        # file_handler = logging.FileHandler('../log/rakuten_games.log')
        file_handler.setLevel(logging.INFO)
        file_handler.setFormatter(formatter)
        logger.addHandler(file_handler)
    return logger

def customTime(*args):
    return datetime.now(timezone('Asia/Tokyo')).timetuple()