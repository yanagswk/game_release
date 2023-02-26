
import subprocess
import math
import argparse


# 全件数を30で割った商を繰り上げた数字分、実行する



# subprocess.call('python3 py/batch/rakuten_books.py'.split())

def run(item_count: int):
    
    page_count = math.ceil(item_count / 30)
    print(page_count)
    
    for i in range(page_count):
        i+=1
        subprocess.call(f"python3 py/batch/rakuten_books.py {i}".split())

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument("item_count", help="件数")
    args = parser.parse_args()
    print("item_count=" + args.item_count)

    run(int(args.item_count))

