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
        // if (is_array($games)) {
            // foreach($games as $index => $game) {
                $date  = new Carbon($sales_date);
                // 最後の2桁が「00」の場合
                if (mb_strpos($sales_date, "00", 6)) {
                    return "{$date->format('Y年m月')}中";
                } else {
                    return $date->format('Y年m月d日');
                }
            // }
                // return $games;
        // } else {

        // }

    }
}

?>
