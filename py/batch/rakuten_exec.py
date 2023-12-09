
import subprocess
import math
import argparse
import sys


# 全件数を30で割った商を繰り上げた数字分、実行する



# subprocess.call('python3 py/batch/rakuten_books.py'.split())

def run(category: str, item_count: int):
    
    page_count = math.ceil(item_count / 30)
    print(page_count)
    
    # for i in range(8, page_count):
    for i in range(page_count):
        i+=1
        if category == "BOOK":
            subprocess.call(f"python3 py/batch/rakuten_books.py {i}".split())
        elif category == "GAME":
            subprocess.call(f"python3 py/batch/rakuten_game.py {i}".split())
        elif category == "CD":
            subprocess.call(f"python3 py/batch/rakuten_cds.py {i}".split())
        elif category == "DVD":
            subprocess.call(f"python3 py/batch/rakuten_dvd_blu_ray.py {i}".split())

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument("category", help="カテゴリー")
    parser.add_argument("item_count", help="件数")
    args = parser.parse_args()

    caregory_list = ["BOOK", "GAME", "CD", "DVD"]
    if not args.category in caregory_list:
        print("対象のカテゴリーを指定してください")
        sys.exit()

    print("item_count=" + args.item_count)

    run(args.category, int(args.item_count))

