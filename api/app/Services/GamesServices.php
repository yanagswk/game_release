<?php

namespace App\Services;

use Carbon\Carbon;

class GamesServices
{

    /**
     * 発売日のフォーマットを整える
     *
     * @return void
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

    /**
     * yyyy/mm/ddのフォーマットにする
     */
    public function formatSlashDate($date)
    {
        $date  = new Carbon($date);
        return $date->format('Y/m/d');
    }
}

?>
