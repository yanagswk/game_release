<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    use HasFactory;

    // 発売前
    public const BEFORE_RELEASE = 1;
    // 発売後
    public const AFTER_RELEASE = 2;

    protected $table = 'games';

    protected $fillable = [
        'title',
        'hardware',
        'price',
        'sales_date',
        'large_image_url',
        'item_url',
        'affiliate_url',
        'label',
        'item_caption',
        'review_count',
        'review_average',

        'genre_detail',
        'release_date',
        // 'genre_detail',
        // 'genre_detail',
    ];

    // 取得しない項目
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


    // お気に入りテーブルとのリレーション
    public function favorite()
    {
        return $this->hasMany('App\Models\FavoriteGames', 'games_id', 'id');
    }

    // 通知テーブルとのリレーション
    public function notification()
    {
        return $this->hasOne('App\Models\Notification', 'game_id', 'id');
    }

    // 通知テーブルとのリレーション
    public function game_image()
    {
        return $this->hasMany('App\Models\GameImage', 'game_id', 'id');
    }

    /**
     * スコープ 有効なゲームのみ
     *
     * @param [type] $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->where('disabled', false);
    }


    /**
     * スコープ 発売日(sales_date)が決まっていないゲームを除外
     * 例)「20221200」とか「20220000」など
     *
     * @param [type] $query
     * @return void
     */
    public function scopeSalesDateAllZero($query)
    {
        return $query->where('sales_date', 'not like', '%00');
    }
}
