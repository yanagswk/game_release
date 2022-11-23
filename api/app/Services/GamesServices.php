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
        // 最後の2桁が「00」の場合
        if (mb_strpos($sales_date, "00", 6)) {
            return "{$date->format('Y年m月')}中";
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
