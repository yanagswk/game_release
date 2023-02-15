import datetime
import mysql.connector
import pytz
import os
from datetime import datetime as dt
from dotenv import load_dotenv

from log_setting import getMyLogger

load_dotenv()


class ReleasedModel():
    def __init__(self) :
        self.__connection = mysql.connector.connect(
            host = os.getenv('DB_HOST'),
            user = os.getenv('DB_USER'),
            passwd = os.getenv('DB_PASSWD'),
            db = os.getenv('DB_NAME')
        )
        self.__connection.autocommit = False
        self.__cursor = self.__connection.cursor(dictionary=True)
        self.logger = getMyLogger(__name__)
        
        
    def get_not_exit_game_image(self, offset: int) -> list:
        """
            ゲーム画像のurlがないゲームを取得するクエリ
            
            Parameters
            ----------
            offset : int
                いくつ取得するか
        """

        games = []
        try:
            sql = ('''
                select `games`.`id`, `games`.`title`, `games`.`item_url` from `games` 
                    where not exists (
                        select * from `game_image` 
                        where `games`.`id` = `game_image`.`game_id` 
                        or `games`.`disabled` = 1
                    )
                order by id DESC
                limit 700
                offset %s
            ''')
            
            param = (offset,)

            #sql実行
            self.__cursor.execute(sql, param)
            # データ取得
            games = self.__cursor.fetchall()
            
            print(f'{self.__cursor.rowcount} 件取得しました。')

            self.__cursor.close()

        except Exception as e:
            print(f"Error Occurred: {e}")

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()
        
        return games

    def exit_date_game_article(self, site_id: int,date: datetime) -> bool:
        """
            指定された日付のゲーム記事が存在するか
            
            Parameters
            ----------
            site_id : int
                サイトid (1: 4gamer, 2: ファミ通)
            date : str
                日付
        """

        games = False
        try:
            sql = ('''
                SELECT EXISTS(
                    SELECT * FROM game_article
                    WHERE DATE_FORMAT(post_date, '%Y%m%d') = DATE_FORMAT(%s, '%Y%m%d')
                    AND site_id = %s
                ) AS game_article_check;
            ''')
            
            param = (date, site_id)

            #sql実行
            self.__cursor.execute(sql, param)
            # データ取得
            games = self.__cursor.fetchall()
            games = True if games[0]["game_article_check"] else False

            self.__cursor.close()

        except Exception as e:
            print(f"Error Occurred: {e}")

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()
        
        return games

    def get_exit_game_article(self, site_id: int, date: datetime):
        """
            指定された日付のゲームを取得
            
            Parameters
            ----------
            site_id : int
                サイトid (1: 4gamer, 2: ファミ通)
            date : str
                日付
        """

        games = ""
        try:
            sql = ('''
                SELECT title FROM game_article
                WHERE DATE_FORMAT(post_date, '%Y%m%d') = DATE_FORMAT(%s, '%Y%m%d')
                AND site_id = %s;
            ''')
            
            param = (date, site_id)

            #sql実行
            self.__cursor.execute(sql, param)
            # データ取得
            games = self.__cursor.fetchall()
            # games = True if games[0]["game_article_check"] else False

            self.__cursor.close()

        except Exception as e:
            print(f"Error Occurred: {e}")

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()
        
        return games

        
    def insert_game_image(self, game_id: int, image_list: list, genre: str):
        """
            ゲームの写真をインサートする

            Parameters
            ----------
            game_id : list
                ゲームid
            image_list : list
                画像url一覧
            genre : list
                ジャンル名
        """
        # ジャンル
        game_genre_sql = '''
            UPDATE games SET genre = %s
            where id = %s
        '''
        param = (genre, game_id)
        self.__cursor.execute(game_genre_sql, param)
        
        # ゲーム画像インサート
        game_image_sql = '''
            INSERT INTO game_image (game_id, image_type, img_url, created_at, updated_at)
            VALUES (%s,%s,%s,%s,%s)
        '''
        insert_data = []
        try:
            for n in range (len(image_list)):
                insert_data.append((    # tapuleとして配列に追加
                    game_id,
                    n+1,
                    image_list[n],
                    datetime.datetime.now(pytz.timezone('Asia/Tokyo')),
                    datetime.datetime.now(pytz.timezone('Asia/Tokyo')),
                ))
            self.__cursor.executemany(game_image_sql, insert_data)

        except Exception as e:
            self.logger.error(f"Error Occurred: {e}")
            self.logger.error("インサート失敗")
            self.__connection.rollback()
        
        else:
            # コミット
            self.__connection.commit()
            self.logger.info(f"{self.__cursor.rowcount} records inserted for games.game_image")
            self.logger.info("インサート完了")
            self.__cursor.close()

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()
                
    
    def insert_article(self, article_list: list, site_id: int):
        """
            記事をインサートする
        """
        
        sql = '''
            INSERT INTO game_article (site_id, site_url, title, top_image_url, genre, post_date, created_at, updated_at)
            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
        '''
        # tdatetime = dt.strptime(day, '%Y%m%d')   # 日付に変更
        insert_data = []
        try:
            for n in range (len(article_list)):
                insert_data.append((
                    site_id,
                    article_list[n]['url'],
                    article_list[n]['title'],
                    article_list[n]['img'],
                    article_list[n]['genre'],
                    article_list[n]['post_date'],
                    datetime.datetime.now(pytz.timezone('Asia/Tokyo')),
                    datetime.datetime.now(pytz.timezone('Asia/Tokyo')),
                ))
                
            self.__cursor.executemany(sql, insert_data)
                
        except Exception as e:
            print(f"Error Occurred: {e}")
            self.__connection.rollback()
        
        else:
            # コミット
            self.__connection.commit()
            print(f"{self.__cursor.rowcount} records inserted for games.article")
            self.__cursor.close()

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()


    



    def update_game_disable(self, game_id: int):
        """
            ゲームを無効扱いにするクエリ
        """
        
        sql = '''
            UPDATE games SET disabled = 1
            where id = %s
        '''
        try:
            param = (game_id,)
            self.__cursor.execute(sql, param)

        except Exception as e:
            print(f"Error Occurred: {e}")
            self.__connection.rollback()
        
        else:
            # コミット
            self.__connection.commit()
            self.logger.info(f"{self.__cursor.rowcount} records updated for games")
            self.__cursor.close()

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()
        
        
    def get_books_item(self, offset: int) -> list:
        """
            本を取得するクエリ
            
            Parameters
            ----------
            offset : int
                いくつ取得するか
        """

        games = []
        try:
            sql = ('''
                select * from `books_item` 
                order by id DESC
                limit 3
                offset %s
            ''')
            
            param = (offset,)

            #sql実行
            self.__cursor.execute(sql, param)
            # データ取得
            games = self.__cursor.fetchall()
            
            print(f'{self.__cursor.rowcount} 件取得しました。')

            self.__cursor.close()

        except Exception as e:
            print(f"Error Occurred: {e}")

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()
        
        return games




    def insert_books(self, books_list: list):
        """
            本情報をインサートする
        """
        
        sql = '''
            INSERT INTO books_item (
                title, page_url, genre, genre_detail, label, author, image_url, series, page, size,
                description, release_date, publisher, isbn, rakuten_affiliate_url, created_at, updated_at
            )
            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)
        '''
        insert_data = []
        try:
            for n in range (len(books_list)):
                insert_data.append((
                    books_list[n]['title'],
                    books_list[n]['page_url'],
                    books_list[n]['genre'],
                    books_list[n]['genre_detail'],
                    books_list[n]['label'],
                    books_list[n]['author'],
                    books_list[n]['image_url'],
                    books_list[n]['series'],
                    books_list[n]['page'],
                    books_list[n]['size'],
                    books_list[n]['description'],
                    books_list[n]['release_date'],
                    books_list[n]['publisher'],
                    books_list[n]['isbn'],
                    books_list[n]['rakuten_affiliate_url'],
                    datetime.datetime.now(pytz.timezone('Asia/Tokyo')),
                    datetime.datetime.now(pytz.timezone('Asia/Tokyo')),
                ))
                
            self.__cursor.executemany(sql, insert_data)
                
        except Exception as e:
            print(f"Error Occurred: {e}")
            self.__connection.rollback()
        
        else:
            # コミット
            self.__connection.commit()
            print(f"{self.__cursor.rowcount} records inserted for games.article")
            self.__cursor.close()

        finally:
            if self.__connection is not None and self.__connection.is_connected():
                self.__connection.close()