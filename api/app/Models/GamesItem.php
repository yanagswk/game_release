<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamesItem extends Model
{
    use HasFactory;

    // 発売前
    public const BEFORE_RELEASE = 1;
    // 発売後
    public const AFTER_RELEASE = 2;

    protected $table = 'games_item';

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
        'disabled'
    ];

    /**
     * スコープ 抽出制限
     *
     * @param [type] $limit
     * @param [type] $offset
     * @return void
     */
    public function scopePage($query, int $limit, int $offset)
    {
        return $query->limit($limit)->offset($offset);
    }

    /**
     * スコープ 有効
     *
     * @param [type] $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->where('disabled', false);
    }
}
