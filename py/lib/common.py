import re

# sys.path.append(str(Path(__file__).resolve().parent.parent))
# from released_db import ReleasedModel
# from log_setting import getMyLogger
# from lib.chrome import get_my_chrome

class Common:
    """
        共通処理
    """

    @staticmethod
    def format_release_date(release_date_str: str) -> str:
        """
        Returns:
            日付フォーマット
        """
        result = re.sub(r"\D", "", release_date_str)    # 数字のみへ
        return result.ljust(8, '0')                     # 8桁になるように0埋め

