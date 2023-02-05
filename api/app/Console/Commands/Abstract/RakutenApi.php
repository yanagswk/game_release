<?php

namespace App\Console\Commands\Abstract;

trait RakutenApi
{
    /**
     * 楽天apiを叩く
     * @param int $page ページ数(100まで)
     * @param int $size 書式のタイプ(9:コミック)
     * @return array
     */
    abstract public function requestRakutenApi(
        $page,
        $size
    );

    /**
     * 楽天apiの情報を整形する
     * @param array $contents apiデータ
     * @return array
     */
    abstract public function formatRakutenApiBody($contents);

    /**
     * 楽天apiのデータをdbに追加する
     * @param array $contents apiデータ
     * @return void
     */
    abstract public function addRakutenApiData($contents);

    /**
     * 発売日をフォーマットする
     * 2023年10月05日 => 20231005
     *
     * @param String $sales_date
     * @return String
     */
    public function formatSalesDate(String $sales_date)
    {
        // 数字のみにする
        $sales_date_number = preg_replace('/[^0-9]/', '', $sales_date);

        // 8桁になるように0埋め
        return str_pad($sales_date_number, 8, 0, STR_PAD_RIGHT);
    }
}
?>
