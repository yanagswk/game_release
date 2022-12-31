import mysql.connector

# from .base_model import BaseModel


class ReleasedModel():
    def __init__(self) :
        self.__connection = mysql.connector.connect(
            host='db',
            user='game_release',
            passwd='game_release_pass',
            db='game_release_db'
        )

        self.__cursor = self.__connection.cursor()


    def bulk_insert_kaden_list(self, values: list, site: str):
        """
            架電データ追加
        """

        if len(values) == 0:
            return True

        # 接続確認
        if not self.__connection.is_connected():
            return True

        try:
            # field_list = ['title', 'business_content', 'ganre', 'pref', 'shop_id', 'name', 'kana', 'tel', 'open', 'close', 'company', 'hp', 'address', 'access', 'detail_url', 'yoyaku_tel', 'official_account']
            field_list = ['title', 'business_content', 'tel', 'company', 'hp', 'address', 'detail_url']
            fields = ','.join(field_list)
            place_holders = ','.join(['%s'] * len(field_list))
            
            sql = f"INSERT INTO kaden_list (site, {fields}) VALUES (%s, {place_holders})"

            val_list = []
            for val in values:
                row = [site]
                for field in field_list:
                    row.append(val.get(field) or '')
                val_list.append(tuple(row))

            # insertを実行
            self.__cursor.executemany(sql, val_list)
            
            
            
            # コミットする
            self.__connection.commit()
            # 実行結果
            print(f"{self.__cursor.rowcount} 件追加しました。")

        except Exception as e:
            
            print(self.__cursor._executed)
            
            print(f"Error Occurred: {e}")


    def create_kaden_list_table(self):
        """
            kaden_listテーブルが存在しなければ作成
        """
        try:
            sql = '''
                CREATE TABLE IF NOT EXISTS kaden_list(
                id INT(11) AUTO_INCREMENT NOT NULL, 
                title VARCHAR(1000),
                company VARCHAR(1000),
                tel VARCHAR(1000), 
                business_content VARCHAR(1000), 
                address VARCHAR(1000), 
                hp VARCHAR(1000), 
                detail_url VARCHAR(1000),
                site VARCHAR(100),
                PRIMARY KEY (id)
                )'''
            self.__cursor.execute(sql)

            self.__cursor.execute("SHOW TABLES")
            print(self.__cursor.fetchall())

        except Exception as e:
            print(f"Error table create: {e}")
            
            
            
            
            
            
            
    def insert_game_info(self, values: list):
        try:
            field_list = ['title', 'hardware', 'image_url', 'release_date', 'contents']
            fields = ','.join(field_list)
            place_holders = ','.join(['%s'] * len(field_list))
            
            sql = f"INSERT INTO game_info_list ({fields}) VALUES {tuple(values)}"

            # insertを実行
            # self.__cursor.executemany(sql, val_list)
            self.__cursor.execute(sql)
            
            # コミットする
            self.__connection.commit()

        except Exception as e:            
            print(f"Error Occurred: {e}")


    def create_game_info_list_table(self):
        """
            game_info_listテーブルが存在しなければ作成
        """
        try:
            sql = '''
                CREATE TABLE IF NOT EXISTS game_info_list(
                id INT(11) AUTO_INCREMENT NOT NULL, 
                title VARCHAR(1000),
                hardware VARCHAR(1000),
                image_url VARCHAR(1000),
                release_date VARCHAR(20),
                contents VARCHAR(1000),
                PRIMARY KEY (id)
                )'''
            self.__cursor.execute(sql)

            self.__cursor.execute("SHOW TABLES")
            print(self.__cursor.fetchall())

        except Exception as e:
            print(f"Error table create: {e}")


    def __del__(self):
        self.__cursor.close()
        self.__connection.close()
