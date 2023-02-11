<?php

namespace App\Libraries;

use Carbon\Carbon;

class Common
{
    /**
     * 今日の日付を返す
     * 例) 20220110
     *
     * @return String
     */
    public function getToday()
    {
        $today = Carbon::today();
        return $today->format('Ymd');
    }


    /**
     * 発売日のフォーマットを整える
     *
     * @return String $sales_date 例)20221001
     * @return String
     */
    public function formatSalesDate($sales_date)
    {
        $date  = new Carbon($sales_date);
        $year = mb_substr($sales_date, 0, 4);
        $month = mb_substr($sales_date, 4, 2);

        if ($sales_date == '00000000') {
            // 「00000000」パターン
            return "発売時期不明";
        } else if (str_ends_with($sales_date, '0000')) {
            // 例)「20220000」パターン
            return "{$year}年中";
        } else if (str_ends_with($sales_date, '00')) {
            // 「20221200」
            return "{$year}年{$month}月中";
        } else {
            return $date->format('Y年m月d日');
        }
    }
}

?>
