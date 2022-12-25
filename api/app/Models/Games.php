<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Games extends Model
{
    use HasFactory;

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

}
