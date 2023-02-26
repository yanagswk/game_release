<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DvdBluRayItem extends Model
{
    use HasFactory;

    protected $table = 'dvd_blu_ray_item';

    // public const CD_ID = 1;
    // public const DVD_ID = 2;
    // 発売前
    public const BEFORE_RELEASE = 1;
    // 発売後
    public const AFTER_RELEASE = 2;

    protected $fillable = [
        'title',
        'page_url',
        'type',
        'genre',
        'genre_detail',
        'author',
        'relation_item',
        'selling_agency',
        'distributor',
        'disc_count',
        // 'music_count',
        'record_time',
        'cd_number',
        'jan',
        // 'in_store_code',
        'release_date',
        'image_url',
        'description',
        'rakuten_affiliate_url',
        'amazon_affiliate_url',
        'disabled',
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
