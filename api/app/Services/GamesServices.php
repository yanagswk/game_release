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
    public function formatSalesDate($games)
    {
        foreach($games as $game) {
            $date  = new Carbon($game['sales_date']);
            // 最後の2桁が「00」の場合
            if (mb_strpos($game['sales_date'], "00", 6)) {
                $game['sales_date'] = "{$date->format('Y年m月')}中";
            } else {
                $game['sales_date'] = $date->format('Y年m月d日');
            }
        }
        return $games;
    }
}

?>
